<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $selectedConversationId = null;
    public $newMessage = '';

    public function selectConversation($id): void
    {
        $this->selectedConversationId = (int) $id;

        // تحديث مباشر بدون جلب السجل كاملاً
        Conversation::where('id', $id)->update(['participant_unread_count' => 0]);

        $conversationType = Conversation::where('id', $id)->value('type') ?? 'support';

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

        Conversation::where('id', $this->selectedConversationId)->increment('user_unread_count', 1, [
            'last_message_id' => $message->id,
            'last_message_at' => now(),
        ]);

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
        $conversations = Conversation::query()
            ->select(['id', 'user_id', 'driver_id', 'type', 'last_message_id', 'last_message_at', 'participant_unread_count', 'updated_at'])
            ->with([
                'user:id,name,phone',
                'driver:id,name,phone',
                'lastMessage:id,body,created_at'
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(25);
        $selectedConversation = null;
        $messages = [];

        if ($this->selectedConversationId) {
            $selectedConversation = Conversation::select(['id', 'user_id', 'driver_id', 'type'])
                ->with(['user:id,name', 'driver:id,name'])
                ->find($this->selectedConversationId);

            $messages = Message::where('conversation_id', $this->selectedConversationId)
                ->select(['id', 'conversation_id', 'sender_type', 'sender_id', 'body', 'created_at'])
                ->with('sender:id,name')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('livewire.chat.index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'messages'  => $messages,
            'totalCount' => $conversations->total(),
        ]);
    }
}