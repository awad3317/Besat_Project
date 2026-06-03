<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentField extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_step_id', 'field_key', 'label', 'hint', 
        'description', 'type', 'min_length', 'max_length', 
        'is_hidden', 'default_value','is_required'
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function step()
    {
        return $this->belongsTo(PaymentStep::class, 'payment_step_id');
    }
}
