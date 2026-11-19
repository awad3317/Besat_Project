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
    protected ?string $reason;
    protected ?string $cancelledByRole;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        TripRequest $tripRequest,
        ?string $reason = null,
        ?string $cancelledByRole = null
    ) {
        $this->tripRequest = $tripRequest;
        $this->reason = $reason;
        $this->cancelledByRole = $cancelledByRole;
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
        if ($this->cancelledByRole === 'ADMIN') {
            $body = "تم إلغاء الرحلة رقم #{$this->tripRequest->id} من قبل الإدارة.";
            if (! empty($this->reason)) {
                $body .= " السبب: {$this->reason}";
            }
        } else {
            $body = 'تم إلغاء طلب الرحلة الخاص بك بنجاح.';
            if (! empty($this->reason)) {
                $body .= " السبب: {$this->reason}";
            }
        }

        return [
            'request_id'        => $this->tripRequest->id,
            'driver_id'         => $this->tripRequest->driver_id,
            'title'             => 'تم إلغاء الرحلة!',
            'body'              => $body,
            'status'            => 'cancelled',
            'cancelled_by'      => $this->tripRequest->cancelled_by,
            'cancelled_by_role' => $this->cancelledByRole ?? 'USER',
            'reason'            => $this->reason,
        ];
    }
}