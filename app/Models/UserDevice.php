<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'device_token', 'device_type', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
