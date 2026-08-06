<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        // تحميل علاقة المرسل لتمرير اسمه للطرف الآخر
        $this->message->load('sender:id,name');
    }

    /**
     * القناة المخصصة للبث
     */
    public function broadcastOn(): array
{
    $conversation = $this->message->conversation;
    $channelPrefix = $conversation->type === 'request' ? 'chat.request.' : 'chat.support.';

    return [
        new Channel($channelPrefix . $conversation->id),
    ];
}

    /**
     * اسم الحدث الذي يستمع له التطبيق في Flutter والداش بورد
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * شكل البيانات المرسلة عبر WebSocket
     */
    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'sender_type'     => $this->message->sender_type === \App\Models\User::class ? 'user' : ($this->message->sender_type === \App\Models\Driver::class ? 'driver' : 'admin'),
            'type'            => $this->message->type,
            'body'            => $this->message->body,
            'attachment_path' => $this->message->attachment_path,
            'metadata'        => $this->message->metadata,
            'created_at'      => $this->message->created_at->toIso8601String(),
        ];
    }
}