<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'trip_id', 'points', 'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Request::class, 'trip_id');
    }

    /**
     * Get a readable Arabic description for a loyalty point transaction.
     */
    public function getDescriptionAttribute(): string
    {
        if (array_key_exists('description', $this->attributes) && $this->attributes['description'] !== null) {
            return (string) $this->attributes['description'];
        }

        if ($this->type === 'converted') {
            return 'تحويل نقاط إلى رصيد المحفظة';
        }

        return $this->trip_id
            ? 'مكافأة نقاط ولاء عن الرحلة #' . $this->trip_id
            : 'مكافأة نقاط ولاء';
    }
}