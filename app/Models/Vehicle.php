<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'description', 'max_passengers'
    ];
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_vehicles');
    }
}
