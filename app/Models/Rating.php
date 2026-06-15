<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'request_id', 'driver_id', 'user_id', 'rating_value','comment'
    ];
    protected $casts = [
        'rating_value' => 'integer',
    ];
    public function request()
    {
        return $this->belongsTo(Request::class,'request_id','id');
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class,'driver_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

}
