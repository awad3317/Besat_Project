<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'cancelled_by',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public static function statusConfig(): array
    {
        return [
            'pending' => [
                'text' => 'قيد الانتظار',
                'class' => 'text-warning-600 bg-warning-50 dark:bg-warning-500/15 dark:text-warning-500',
            ],
            'searching_driver' => [
                'text' => 'جاري البحث عن سائق',
                'class' => 'text-warning-600 bg-warning-50 dark:bg-warning-500/15 dark:text-warning-500',
            ],
            'paused' => [
                'text' => 'بانتظار التوجيه',
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
}
