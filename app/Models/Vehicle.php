<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'description', 'max_passengers','image','min_price'
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
