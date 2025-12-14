<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_whatsapp',
        'user_id',
        'title',
        'price',
        'start_address',
        'end_address',
        'description',
        'driver_id',
        'status',
        'cancellation_reason',
        'created_by',
        'cancelled_by'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public static function statusConfig()
    {
        return [
            'searching_driver' => [
                'text' => 'جاري البحث عن سائق',
                'class' => 'text-warning-600 bg-warning-50 dark:bg-warning-500/15 dark:text-warning-500',
            ],
            'in_progress' => [
                'text' => 'قيد التنفيذ',
                'class' => 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500',
            ],
            'completed' => [
                'text' => 'مكتملة',
                'class' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
            ],
            'cancelled' => [
                'text' => 'ملغية',
                'class' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            ]
        ];
    }
    public function getStatusInfo()
    {
        $config = self::statusConfig();
        return $config[$this->status] ?? $config['pending'];
    }
    public function getStatusTextAttribute()
    {
        return $this->getStatusInfo()['text'];
    }
    public function getStatusClassAttribute()
    {
        return $this->getStatusInfo()['class'];
    }


}
