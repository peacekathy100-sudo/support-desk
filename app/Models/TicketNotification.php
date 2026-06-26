<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Ticket;
use App\Models\SysUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketNotification extends Model
{
    use HasFactory;

    protected $table = 'ticket_notifications';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'type',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'user_id', 'user_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => 1, 'read_at' => now()]);
    }
}
