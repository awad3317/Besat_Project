<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_rate', 'is_active', 'max_uses',  'usage_limit_per_user', 'current_uses'
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
