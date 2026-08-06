<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'request_id',
        'user_id',
        'driver_id',
        'assigned_admin_id',
        'status',
        'last_message_id',
        'last_message_at',
        'user_unread_count',
        'participant_unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}
