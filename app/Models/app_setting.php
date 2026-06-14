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
        'auto_assign_to_drivers',
        'version',
        'android_update_url',
        'ios_update_url',
        'company_website',
        'whatsapp_support',
        'company_email',
        'ref_no',
        'otp_enabled',
        'maintenance_mode',
    ];
    protected $casts = [
        'auto_assign_to_drivers' => 'boolean',
        'maintenance_mode' => 'boolean',
        'otp_enabled' => 'boolean',
    ];
}
