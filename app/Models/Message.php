<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'type',
        'body',
        'attachment_path',
        'metadata',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at'  => 'datetime',
    ];

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
