<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceBooking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'service_type',
        'vehicle_id',
        'client_name',
        'passenger_count',
        'notes',
        'start_address',
        'start_latitude',
        'start_longitude',
        'end_address',
        'end_latitude',
        'end_longitude',
        'vehicles_count',
        'wants_ac',
        'trip_datetime',
        'duration',
        'service_details',
        'status',
        'total_price',
        'payment_status',
    ];
    protected $casts = [
        'service_details' => 'array',
        'wants_ac'        => 'boolean',
        'trip_datetime'   => 'datetime',
        'start_latitude'  => 'float',
        'start_longitude' => 'float',
        'end_latitude'    => 'float',
        'end_longitude'   => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(ServiceBookingStop::class)->orderBy('stop_order');
    }

}
