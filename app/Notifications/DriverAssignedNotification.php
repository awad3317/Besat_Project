<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Request as TripRequest;
class DriverAssignedNotification extends Notification
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {   
        return [
            'request_id'   => $this->tripRequest->id,
            'driver_id'    => $this->tripRequest->driver_id,
            'title'        => 'تم تعيين سائق!',
            'body'         => 'تم تعيين سائق لرحلتك وهو الآن في الطريق إليك.',
            'status'       => 'accepted',
        ];
    }
}
