<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class Driver extends Model
{
    
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'vehicle_id', 'name', 'phone','whatsapp_number','password',
        'city', 'district',
        'vehicle_image', 'driver_image', 'identity_image', 'identity_number','plate_number',
        'is_banned', 'is_online', 'is_active', 'device_token', 'latitude', 'longitude', 
    ];
    protected $hidden = [
        'password',
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed', 
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
