<?php

namespace App\Livewire\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Index extends Component
{
    public $selectedConversationId = null;
    public $newMessage = '';
    public $activeTab = 'clients'; // 'clients' or 'drivers'

    protected $queryString = [
        'selectedConversationId' => ['except' => null, 'as' => 'c'],
        'activeTab' => ['except' => 'clients', 'as' => 'tab'],
    ];

    public function mount()
    {
        if ($this->selectedConversationId) {
            $this->selectConversation($this->selectedConversationId);
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function selectConversation($id): void
    {
        $this->selectedConversationId = (int) $id;

        $conv = Conversation::select(['id', 'user_id', 'driver_id', 'type', 'participant_unread_count'])->find($id);
        $unreadCount = (int) ($conv->participant_unread_count ?? 0);

        if ($conv) {
            $this->activeTab = ($conv->driver_id && !$conv->user_id) ? 'drivers' : 'clients';
        }

        // تحديث مباشر بدون جلب السجل كاملاً
        // استخدام DB::raw('updated_at') لمنع تغيير وقت التحديث، وبالتالي عدم قفز المحادثة لأعلى القائمة
        Conversation::where('id', $id)->update([
            'participant_unread_count' => 0,
            'updated_at' => \Illuminate\Support\Facades\DB::raw('updated_at')
        ]);

        if ($unreadCount > 0) {
            $this->dispatch('chat-messages-read', count: $unreadCount);
        }

        $conversationType = $conv->type ?? 'support';

        $this->dispatch('subscribe-to-channel', conversationId: $id, type: $conversationType);
        $this->dispatch('scroll-to-bottom');
    }

    public function sendMessage(): void
    {
        if (trim($this->newMessage) === '' || !$this->selectedConversationId) {
            return;
        }
        $admin = Auth::user();
        $message = Message::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_type' => get_class($admin),
            'sender_id' => $admin->id,
            'type'=> 'text',
            'body' => $this->newMessage,
        ]);

        $conversation = Conversation::with(['user.devices', 'driver'])->find($this->selectedConversationId);

        if ($conversation) {
            $tokens = [];
            $unreadField = 'user_unread_count';

            // 1. إذا كان المستلم مستخدماً (User)
            if ($conversation->user) {
                $user = $conversation->user;
                $unreadField = 'user_unread_count';

                if ($user->is_notifications_enabled) {
                    // الاعتماد على Collection المحملة مسبقاً دون استعلام إضافي
                    $tokens = $user->devices
                        ->pluck('device_token')
                        ->filter()
                        ->all();
                }
            } 
            // 2. إذا كان المستلم سائقاً (Driver)
            elseif ($conversation->driver) {
                $driver = $conversation->driver;
                $unreadField = 'participant_unread_count';

                if (!empty($driver->device_token) && !$driver->is_banned) {
                    $tokens[] = $driver->device_token;
                }
            }

            // تحديث آخر رسالة وزيادة العداد للطرف المستلم الصحيح
            $conversation->increment($unreadField, 1, [
                'last_message_id' => $message->id,
                'last_message_at' => now(),
            ]);

            // تنظيف التوكنات وتفادي التكرار
            $tokens = array_unique(array_filter($tokens));

            // إرسال الإشعارات عبر Firebase
            foreach ($tokens as $token) {
                try {
                    app(FirebaseService::class)->sendNotification(
                        deviceToken: $token,
                        title: 'الدعم الفني',
                        body: 'لديك رسالة جديدة من الدعم الفني: ' . mb_substr($message->body, 0, 50),
                        data: [
                            'type'            => 'support_chat',
                            'new_message'     => 'true',
                            'new_request'     => 'false',
                            'conversation_id' => (string) $this->selectedConversationId,
                            'message_id'      => (string) $message->id,
                        ]
                    );
                } catch (\Throwable $e) {
                    Log::warning("فشل إرسال إشعار FCM للتوكن {$token}: " . $e->getMessage());
                }
            }
        }

        broadcast(new MessageSent($message));

        $this->newMessage = '';
        $this->dispatch('clear-pending');
        $this->dispatch('scroll-to-bottom');
    }

    public function closeConversation(): void
    {
        if (!$this->selectedConversationId) return;

        Conversation::where('id', $this->selectedConversationId)->update(['status' => 'closed']);

        $this->selectedConversationId = null;
    }

    public function handleIncomingMessage(): void
    {
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        $baseQuery = fn () => Conversation::query()
            ->select(['id', 'user_id', 'driver_id', 'type', 'last_message_id', 'last_message_at', 'participant_unread_count', 'updated_at'])
            ->with([
                'user:id,name,phone',
                'driver:id,name,phone,driver_image',
                'lastMessage:id,body,created_at'
            ])
            ->orderBy('updated_at', 'desc');

        $clientConversations = $baseQuery()->whereNotNull('user_id')->limit(50)->get();
        $driverConversations = $baseQuery()->whereNotNull('driver_id')->whereNull('user_id')->limit(50)->get();

        $selectedConversation = null;
        $messages = [];

        if ($this->selectedConversationId) {
            $selectedConversation = Conversation::select(['id', 'user_id', 'driver_id', 'type'])
                ->with(['user:id,name', 'driver:id,name,driver_image'])
                ->find($this->selectedConversationId);

            $messages = Message::where('conversation_id', $this->selectedConversationId)
                ->select(['id', 'conversation_id', 'sender_type', 'sender_id', 'body', 'created_at'])
                ->with('sender:id,name')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $clientsUnread = $clientConversations->where('participant_unread_count', '>', 0)->count();
        $driversUnread = $driverConversations->where('participant_unread_count', '>', 0)->count();

        return view('livewire.chat.index', [
            'clientConversations' => $clientConversations,
            'driverConversations' => $driverConversations,
            'clientsCount' => $clientConversations->count(),
            'driversCount' => $driverConversations->count(),
            'selectedConversation' => $selectedConversation,
            'messages'  => $messages,
            'clientsUnread' => $clientsUnread,
            'driversUnread' => $driversUnread,
        ]);
    }
}
