<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Models\Driver;
use App\Models\Message;
use App\Models\User;
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
    /**
     * الحصول على محادثة الدعم الفني الخاصة بالمستخدم أو السائق أو إنشاؤها.
     * تقبل Model السائق أو المستخدم
     *
     * @param User|Driver $sender
     * @return Conversation
     */
    public function getOrCreateSupportConversation(User|Driver $sender): Conversation
    {
        $isDriver = $sender instanceof Driver;

        $attributes = [
            'type'   => 'support',
            'status' => 'open',
        ];

        if ($isDriver) {
            $attributes['driver_id'] = $sender->id;
            $attributes['user_id']   = null;
        } else {
            $attributes['user_id']   = $sender->id;
            $attributes['driver_id'] = null;
        }

        return Conversation::firstOrCreate(
            $attributes,
            [
                'status'                   => 'open',
                'user_unread_count'        => 0,
                'participant_unread_count' => 0,
            ]
        );
    }

    public function storeMessage(Conversation $conversation, object $sender, array $data): Message
    {
        return DB::transaction(function () use ($conversation, $sender, $data) {
            // 1. حفظ الرسالة (Polymorphic sender)
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

            // تحديد طرف الإرسال لتحديث العداد الصحيح
            $isCustomerOrDriverOwner = false;

            if ($conversation->type === 'support') {
                // إذا كان المرسل هو صاحب المحادثة (العميل أو السائق)، فإن الإشعار يذهب للإدارة
                if ($conversation->driver_id && get_class($sender) === Driver::class && $sender->id === $conversation->driver_id) {
                    $isCustomerOrDriverOwner = true;
                } elseif ($conversation->user_id && get_class($sender) === User::class && $sender->id === $conversation->user_id) {
                    $isCustomerOrDriverOwner = true;
                }

                if ($isCustomerOrDriverOwner) {
                    $updateData['participant_unread_count'] = DB::raw('participant_unread_count + 1'); // المشرف
                } else {
                    $updateData['user_unread_count'] = DB::raw('user_unread_count + 1'); // صاحب الشات
                }
            } else {
                // محادثات الرحلات العادية (Request)
                if (get_class($sender) === User::class && $sender->id === $conversation->user_id) {
                    $updateData['participant_unread_count'] = DB::raw('participant_unread_count + 1');
                } else {
                    $updateData['user_unread_count'] = DB::raw('user_unread_count + 1');
                }
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
        $isUser = get_class($user) === User::class && $user->id === $conversation->user_id;
        $isDriver = get_class($user) === Driver::class && $user->id === $conversation->driver_id;

        if ($isUser || $isDriver) {
            $conversation->update(['user_unread_count' => 0]);
        } else {
            $conversation->update(['participant_unread_count' => 0]);
        }

        // تحديث read_at للرسائل القادمة من الطرف الآخر
        $conversation->messages()
            ->where('sender_type', '!=', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}