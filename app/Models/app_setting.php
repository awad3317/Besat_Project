<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class app_setting extends Model
{
    use HasFactory;
    protected $table = 'app_settings';

    protected $fillable = [
        'commission_rate',
    ];
}
