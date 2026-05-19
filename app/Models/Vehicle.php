<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'description', 'max_passengers','image','min_price', 'ac_price_per_km', 'has_ac_option'
    ];
    protected $casts = [
        'has_ac_option' => 'boolean',
        'min_price' => 'float',
        'ac_price_per_km' => 'float',
    ];
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
    public function pricing()
    {
        return $this->hasMany(vehicle_pricing::class);
    }

}
