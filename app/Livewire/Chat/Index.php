<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    #[Url(except: 'all')]
    public $filter = 'all'; // all, support, request

    public $search = '';
    public $selectedConversationId = null;
    public $newMessage = '';

    public function applyFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function selectConversation($id): void
    {
        $this->selectedConversationId = (int) $id;

        $conversation = Conversation::find($id);
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

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->update([
                'last_message_id'   => $message->id,
                'last_message_at'   => now(),
                'user_unread_count' => $conversation->user_unread_count + 1,
            ]);
        }

        broadcast(new MessageSent($message))->toOthers();

        $this->newMessage = '';
        $this->dispatch('scroll-to-bottom');
    }

    public function closeConversation(): void
    {
        if (!$this->selectedConversationId) return;

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation) {
            $conversation->update(['status' => 'closed']);
        }

        $this->selectedConversationId = null;
    }

    public function handleIncomingMessage(): void
    {
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        $conversations = Conversation::query()
            ->with(['user', 'driver', 'lastMessage'])
            ->when($this->filter !== 'all', function ($query) {
                $query->where('type', $this->filter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($u) {
                        $u->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                    })->orWhereHas('driver', function ($d) {
                        $d->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        $selectedConversation = $this->selectedConversationId 
            ? Conversation::with(['user', 'driver', 'admin'])->find($this->selectedConversationId) 
            : null;

        $messages = $this->selectedConversationId 
            ? Message::where('conversation_id', $this->selectedConversationId)
                ->with('sender:id,name')
                ->orderBy('created_at', 'asc')
                ->get() 
            : [];

        return view('livewire.chat.index', [
            'conversations'        => $conversations,
            'selectedConversation' => $selectedConversation,
            'messages'             => $messages,
            'totalCount'           => Conversation::count(),
        ]);
    }
}