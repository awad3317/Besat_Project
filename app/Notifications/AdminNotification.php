<?php

namespace App\Notifications;

use App\Models\Request as TripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    use Queueable;

    protected ?TripRequest $tripRequest;
    protected string $title;
    protected string $body;
    protected ?string $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        ?TripRequest $tripRequest = null,
        string $title = 'طلب رحلة جديد',
        string $body = 'يوجد طلب رحلة جديد بانتظار التعيين اليدوي.',
        ?string $url = null
    ) {
        $this->tripRequest = $tripRequest;
        $this->title = $title;
        $this->body = $body;
        $this->url = $url ?? ($tripRequest ? route('request.show', $tripRequest->id) : route('request.index'));
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
        return $this->toDatabase($notifiable);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'request_id'    => $this->tripRequest?->id,
            'title'         => $this->title,
            'body'          => $this->body,
            'start_address' => $this->tripRequest?->start_address,
            'end_address'   => $this->tripRequest?->end_address,
            'final_price'   => $this->tripRequest?->final_price,
            'url'           => $this->url,
            'type'          => 'manual_dispatch_request',
        ];
    }
}
