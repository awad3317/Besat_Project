<?php

namespace App\Notifications;

use App\Models\Request as TripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TripAcceptedNotification extends Notification
{
    use Queueable;

    protected TripRequest $tripRequest;
    protected ?int $conversationId;

    /**
     * Create a new notification instance.
     */
    public function __construct(TripRequest $tripRequest, ?int $conversationId = null)
    {
        $this->tripRequest = $tripRequest;
        $this->conversationId = $conversationId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // جلب اسم السائق إن كانت العلاقة محملة، أو من الموديل مباشرة
        $driverName = $this->tripRequest->driver?->name ?? 'السائق';

        return [
            'request_id' => $this->tripRequest->id,
            'driver_id' => $this->tripRequest->driver_id,
            'conversation_id' => $this->conversationId,
            'title' => 'تم قبول الرحلة!',
            'body' => "تم قبول طلب الرحلة الخاص بك، الكابتن {$driverName} في طريقه إليك الآن.",
            'status'=> 'accepted',
        ];
    }
}