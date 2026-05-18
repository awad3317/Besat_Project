<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStop extends Model
{
    protected $fillable = [
        'request_id', 'latitude', 'longitude', 'stop_order'
    ];
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}
