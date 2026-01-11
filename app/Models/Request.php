<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'driver_id', 'discount_code_id',
        'start_latitude', 'start_longitude', 'start_address',
        'app_commission_amount', 'vehicle_id', 'created_by_user',
        'created_by','title', 'surcharge_amount',
        'end_latitude', 'end_longitude', 'end_address', 'status',
        'original_price', 'discount_amount', 'final_price',
        'distance_km', 'notes', 'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class, 'discount_code_id');
    }

    public function surcharges()
    {
        return $this->belongsToMany(Surcharge::class, 'request_surcharge')
                    ->withPivot('amount')
                    ->withTimestamps();
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
