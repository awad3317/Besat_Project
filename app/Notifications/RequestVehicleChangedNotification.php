<?php

namespace App\Notifications;

use App\Models\Request as TripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestVehicleChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected TripRequest $tripRequest,
        protected string $oldVehicleType,
        protected string $newVehicleType,
        protected string $adminName,
        protected ?int $oldVehicleId = null,
        protected ?int $newVehicleId = null,
        protected ?float $oldFinalPrice = null,
        protected ?float $newFinalPrice = null,
    ) {}

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
        $newPrice = $this->newFinalPrice ?? $this->tripRequest->final_price;

        return [
            'request_id'       => $this->tripRequest->id,
            'old_vehicle_id'   => $this->oldVehicleId,
            'new_vehicle_id'   => $this->newVehicleId,
            'old_vehicle_type' => $this->oldVehicleType,
            'new_vehicle_type' => $this->newVehicleType,
            'old_final_price'  => $this->oldFinalPrice,
            'new_final_price'  => $newPrice,
            'changed_by'       => $this->adminName,
            'title'            => 'تم تغيير نوع المركبة في طلبك',
            'body'             => "تم تغيير نوع المركبة في الطلب رقم #{$this->tripRequest->id} من ({$this->oldVehicleType}) إلى ({$this->newVehicleType}) وأصبح سعر الرحلة ({$newPrice} ريال)، الدعم الفني: {$this->adminName}",
            'status'           => $this->tripRequest->status,
        ];
    }
}

