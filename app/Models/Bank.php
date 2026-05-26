<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    protected $fillable = [
        'method_key',
        'name',
        'account_name',
        'account_number',
        'color',
        'logo',
        'is_active',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset($this->logo) : null;
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function currencies()
    {
        return $this->belongsToMany(Currency::class);
    }
    public function steps()
    {
        return $this->hasMany(PaymentStep::class)->orderBy('sort_order');
    }
}
