<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'features', 'base_price', 'price_per_km',
        'is_active', 'vehicle_type'
    ];
    protected $casts = [
        'features' => 'array'
    ];

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'service_vehicles');
    }


}
