<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_rate',
        'max_discount_amount',
        'min_trip_fare',
        'is_active',
        'max_uses',
        'usage_limit_per_user',
        'current_uses'
    ];

    protected $casts = [
        'discount_rate'       => 'float',
        'max_discount_amount' => 'float',
        'min_trip_fare'       => 'float',
        'is_active'           => 'boolean',
        'max_uses'            => 'integer',
        'usage_limit_per_user'=> 'integer',
        'current_uses'        => 'integer',
    ];

    public function requests()
    {
        return $this->hasMany(Request::class, 'discount_code_id');
    }
    

    public function users()
    {
        return $this->belongsToMany(User::class, 'discount_code_user', 'discount_code_id', 'user_id')
                    ->withPivot('usage_count');
    }
}
