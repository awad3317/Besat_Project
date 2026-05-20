<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'account_name',
        'account_number',
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
}
