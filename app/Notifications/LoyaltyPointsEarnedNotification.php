<?php

namespace App\Notifications;

use App\Models\Request as TripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoyaltyPointsEarnedNotification extends Notification
{
    use Queueable;

    protected TripRequest $tripRequest;
    protected int $earnedPoints;
    protected string $title;
    protected string $body;

    /**
     * Create a new notification instance.
     */
    public function __construct(TripRequest $tripRequest, int $earnedPoints)
    {
        $this->tripRequest = $tripRequest;
        $this->earnedPoints = $earnedPoints;
        $this->title = 'ألف مبروك! كسبت نقاط ولاء';
        $this->body = "ألف مبروك! لقد كسبت {$earnedPoints} نقطة ولاء مقابل إتمام رحلتك رقم #{$tripRequest->id}.";
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
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
        return [
            'type'       => 'loyalty_points',
            'request_id' => $this->tripRequest->id,
            'points'     => $this->earnedPoints,
            'title'      => $this->title,
            'body'       => $this->body,
        ];
    }
}
