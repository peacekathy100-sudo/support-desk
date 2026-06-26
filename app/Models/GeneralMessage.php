<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralMessage extends Model
{
    use SoftDeletes;

    protected $table = 'general_messages';

    protected $fillable = [
        'sender_id',
        'sender_type',
        'subject',
        'body',
        'status',
        'replied_by',
        'reply_body',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->morphTo('sender', 'sender_type', 'sender_id');
    }

    public function repliedByUser()
    {
        return $this->belongsTo(SysUser::class, 'replied_by', 'user_id');
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isRead(): bool
    {
        return $this->status === 'read';
    }

    public function isReplied(): bool
    {
        return $this->status === 'replied';
    }

    public function getSenderName(): string
    {
        if ($this->sender_type === ExternalClient::class) {
            return $this->sender?->full_name ?? 'Unknown Client';
        }
        return $this->sender?->full_name ?? 'Unknown User';
    }

    public function getSenderEmail(): string
    {
        return $this->sender?->email ?? 'N/A';
    }
}
