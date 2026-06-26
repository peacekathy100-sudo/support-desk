<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MessageReaction extends Model
{
    protected $table = 'message_reactions';

    protected $fillable = [
        'message_id',
        'user_id',
        'user_type',
        'reaction',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
