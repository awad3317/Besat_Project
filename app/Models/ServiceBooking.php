<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceBooking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'service_type',
        'vehicle_id',
        'client_name',
        'passenger_count',
        'notes',
        'start_address',
        'start_latitude',
        'start_longitude',
        'end_address',
        'end_latitude',
        'end_longitude',
        'vehicles_count',
        'wants_ac',
        'trip_datetime',
        'duration',
        'service_details',
        'status',
        'total_price',
        'payment_status',
    ];
    protected $casts = [
        'service_details' => 'array',
        'wants_ac'        => 'boolean',
        'trip_datetime'   => 'datetime',
        'start_latitude'  => 'float',
        'start_longitude' => 'float',
        'end_latitude'    => 'float',
        'end_longitude'   => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(ServiceBookingStop::class)->orderBy('stop_order');
    }

    public static function serviceTypes(): array
    {
        return [
            'events'         => 'فعالياتنا',
            'zafona'         => 'زفونا',
            'rehla'          => 'رحلة',
            'safety_airport' => 'بالسلامة',
            'dawam'          => 'دوام',
            'moshwarana'     => 'مشوارنا',
        ];
    }

    public function getServiceTypeTextAttribute(): string
    {
        return self::serviceTypes()[$this->service_type] ?? ($this->service_type ?: 'غير محدد');
    }

    public static function statusConfig(): array
    {
        return [
            'pending' => [
                'text'  => 'قيد الانتظار',
                'class' => 'text-warning-600 bg-warning-50 dark:bg-warning-500/15 dark:text-warning-500',
            ],
            'approved' => [
                'text'  => 'تمت الموافقة',
                'class' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
            ],
            'in_progress' => [
                'text'  => 'قيد التنفيذ',
                'class' => 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500',
            ],
            'completed' => [
                'text'  => 'مكتملة',
                'class' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
            ],
            'rejected' => [
                'text'  => 'مرفوض',
                'class' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            ],
            'cancelled' => [
                'text'  => 'ملغية',
                'class' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            ],
        ];
    }

    public function getStatusInfo(): array
    {
        $config = self::statusConfig();
        return $config[$this->status] ?? $config['pending'];
    }

    public function getStatusTextAttribute(): string
    {
        return $this->getStatusInfo()['text'];
    }

    public function getStatusClassAttribute(): string
    {
        return $this->getStatusInfo()['class'];
    }

    public function getPaymentStatusTextAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'           => 'مدفوع',
            'partially_paid' => 'مدفوع جزئياً',
            default          => 'غير مدفوع',
        };
    }

    public function getPaymentStatusClassAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'           => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
            'partially_paid' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-500',
            default          => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
