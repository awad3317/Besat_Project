<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ChatRepository
{
    /**
     * الحصول على محادثة الطلب (بين المستخدم والسائق) أو إنشاؤها إذا لم تكن موجودة.
     */
    public function getOrCreateOrderConversation(int $requestId, int $userId, int $driverId): Conversation
    {
        return Conversation::firstOrCreate(
            [
                'type'     => 'request',
                'request_id' => $requestId,
            ],
            [
                'user_id'   => $userId,
                'driver_id' => $driverId,
                'status'    => 'open',
            ]
        );
    }

    /**
     * الحصول على محادثة الدعم الفني الخاصة بالمستخدم أو إنشاؤها.
     */
    public function getOrCreateSupportConversation(int $userId): Conversation
    {
        return Conversation::firstOrCreate(
            [
                'type'    => 'support',
                'user_id' => $userId,
                'status'  => 'open',
            ]
        );
    }

    /**
     * حفظ الرسالة الجديدة وتحديث إحصائيات المحادثة تزامناً.
     */
    public function storeMessage(Conversation $conversation, object $sender, array $data): Message
    {
        return DB::transaction(function () use ($conversation, $sender, $data) {
            // 1. حفظ الرسالة
            $message = $conversation->messages()->create([
                'sender_type'     => get_class($sender),
                'sender_id'       => $sender->id,
                'type'            => $data['type'] ?? 'text',
                'body'            => $data['body'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'metadata'        => $data['metadata'] ?? null,
            ]);

            // 2. تحديث مرجع آخر رسالة وزيادة عداد غير المقروء للمستلِم
            $updateData = [
                'last_message_id' => $message->id,
                'last_message_at' => $message->created_at,
            ];

            // زيادة العداد بحسب الطرف المستلِم
            if (get_class($sender) === \App\Models\User::class && $sender->id === $conversation->user_id) {
                $updateData['participant_unread_count'] = DB::raw('participant_unread_count + 1');
            } else {
                $updateData['user_unread_count'] = DB::raw('user_unread_count + 1');
            }

            $conversation->update($updateData);

            return $message;
        });
    }

    /**
     * تصفير العداد وتحديث حالة القراءة عند فتح الشات.
     */
    public function markAsRead(Conversation $conversation, object $user): void
    {
        $isUser = get_class($user) === \App\Models\User::class && $user->id === $conversation->user_id;

        if ($isUser) {
            $conversation->update(['user_unread_count' => 0]);
        } else {
            $conversation->update(['participant_unread_count' => 0]);
        }

        // تحديث read_at للرسائل القادمة من الطرف الآخر فقط
        $conversation->messages()
            ->where('sender_type', '!=', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}