<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Request as TripRequest;

class TripCancelledNotification extends Notification
{
    use Queueable;

    protected $tripRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(TripRequest $tripRequest)
    {
        $this->tripRequest = $tripRequest;
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
    public function toArray($notifiable): array
    {   
        return [
            'request_id'   => $this->tripRequest->id,
            'driver_id'    => $this->tripRequest->driver_id,
            'title'        => 'تم إلغاء الرحلة!',
            'body'         => 'تم إلغاء طلب الرحلة الخاص بك بنجاح.',
            'status'       => 'cancelled',
            'cancelled_by' => $this->tripRequest->cancelled_by,
        ];
    }
}