<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PinnedMessage extends Model
{
    protected $table = 'pinned_messages';

    protected $fillable = [
        'message_id',
        'conversation_id',
        'pinned_by_id',
        'pinned_by_type',
        'pin_reason',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function pinnedBy(): MorphTo
    {
        return $this->morphTo();
    }
}
