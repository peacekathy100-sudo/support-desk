<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TypingIndicator extends Model
{
    protected $table = 'typing_indicators';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'user_type',
        'started_at',
        'expires_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function isActive(): bool
    {
        return now()->isBefore($this->expires_at);
    }
}
