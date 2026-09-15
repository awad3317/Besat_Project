<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverSettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'admin_id',
        'trips_count',
        'driver_owes_app',
        'app_owes_driver',
        'net_balance',
        'settlement_action',
    ];

    protected $casts = [
        'trips_count'      => 'integer',
        'driver_owes_app'  => 'decimal:2',
        'app_owes_driver'  => 'decimal:2',
        'net_balance'      => 'decimal:2',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'driver_settlement_id');
    }
}