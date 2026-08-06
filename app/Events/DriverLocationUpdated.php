<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $orderId,
        public float $latitude,
        public float $longitude,
        public float $heading
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tracking.order.' . $this->orderId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'   => $this->orderId,
            'latitude'   => $this->latitude,
            'longitude'  => $this->longitude,
            'heading'    => $this->heading,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}