<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Livewire\Component;
use Livewire\WithPagination; // 1. استدعاء الموديول
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination; // 2. إضافة التريت هنا داخل الكلاس

    #[Url(except: 'all')]
    public $filter = 'all';

    public $selectedConversationId = null;
    public $newMessage = '';

    public function applyFilter(string $filter): void
    {
        $this->filter = $filter;
        unset($this->stats, $this->conversations);
        
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    #[Computed(cache: true)]
    public function stats()
    {
        return [
            'total'    => Conversation::count(),
            'support'  => Conversation::where('type', 'support')->count(),
            'request'  => Conversation::where('type', 'request')->count(),
        ];
    }

    #[Computed]
    public function conversations()
    {
        return Conversation::query()
            ->with(['user', 'driver', 'lastMessage'])
            ->when($this->filter !== 'all', function ($query) {
                $query->where('type', $this->filter);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    #[Computed]
    public function selectedConversation()
    {
        if (!$this->selectedConversationId) {
            return null;
        }

        return Conversation::with(['user', 'driver', 'admin'])->find($this->selectedConversationId);
    }

    #[Computed]
    public function messages()
    {
        if (!$this->selectedConversationId) {
            return [];
        }

        return Message::where('conversation_id', $this->selectedConversationId)
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function selectConversation(int $id): void
    {
        $this->selectedConversationId = $id;
        
        unset($this->selectedConversation, $this->messages);

        $conversation = $this->selectedConversation;
        if ($conversation) {
            $conversation->update(['participant_unread_count' => 0]);
            
            $this->dispatch('subscribe-to-channel', conversationId: $id, type: $conversation->type ?? 'support');
            $this->dispatch('scroll-to-bottom');
        }
    }

    public function sendMessage(): void
    {
        if (trim($this->newMessage) === '' || !$this->selectedConversationId) {
            return;
        }

        $admin = Auth::user();

        $message = Message::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_type'     => get_class($admin),
            'sender_id'       => $admin->id,
            'type'            => 'text',
            'body'            => $this->newMessage,
        ]);

        $conversation = $this->selectedConversation;
        if ($conversation) {
            $conversation->update([
                'last_message_id' => $message->id,
                'last_message_at' => now(),
                'user_unread_count' => $conversation->user_unread_count + 1,
            ]);
        }

        broadcast(new MessageSent($message))->toOthers();

        $this->newMessage = '';
        unset($this->stats, $this->conversations, $this->messages, $this->selectedConversation);
        
        $this->dispatch('scroll-to-bottom');
    }

    public function handleIncomingMessage(): void
    {
        unset($this->messages, $this->conversations, $this->selectedConversation);
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.chat.index');
    }
}