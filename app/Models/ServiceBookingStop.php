<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBookingStop extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_booking_id',
        'address_name',
        'latitude',
        'longitude',
        'stop_order',
    ];

    protected $casts = [
        'latitude'   => 'float',
        'longitude'  => 'float',
        'stop_order' => 'integer',
    ];
    public function booking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class, 'service_booking_id');
    }

}
