<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Message Model
 * Represents direct messages between clients and their assigned representatives
 */
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_client_id',
        'sent_to_user_id',
        'sent_by',
        'sent_by_user_id',
        'message',
        'type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client who owns the message
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ExternalClient::class, 'external_client_id', 'id');
    }

    /**
     * Get the user (representative) the message was sent to
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'sent_to_user_id', 'user_id');
    }

    /**
     * Get the admin user who sent the reply
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'sent_by_user_id', 'user_id');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
