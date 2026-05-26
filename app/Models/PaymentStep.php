<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id', 'step_key', 'action_text', 'action_url', 'sort_order'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function fields()
    {
        return $this->hasMany(PaymentField::class);
    }
}
