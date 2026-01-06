<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Surcharge extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'condition_key',
        'time_from',
        'time_to',
        'amount',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
        'time_from' => 'datetime:H:i',
        'time_to' => 'datetime:H:i',
    ];
}
