<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Client;
use App\Models\ExternalClient;
use App\Models\SysUser;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\TicketCategory;
use App\Models\TicketAttachment;
use App\Models\TicketNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $primaryKey = 'ticket_id';

    protected $fillable = [
        'ticket_number',
        'created_by',
        'assigned_to',
        'category_id',
        'subject',
        'description',
        'priority',
        'status',
        'resolution_note',
        'resolved_by',
        'resolved_at',
        'closed_at',
        'due_at',
        'reopened_by',
        'reopened_at',
        'created_from_dept',
        'client_id',
        'external_client_id',
        'created_by_type',
        'chargeable',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
        'due_at'      => 'datetime',
        'reopened_at' => 'datetime',
        'chargeable'  => 'boolean',
    ];

    const PRIORITY_COLORS = [
        'low'    => '#6c757d',
        'normal' => '#17a2b8',
        'high'   => '#fd7e14',
        'urgent' => '#dc3545',
    ];

    const STATUS_COLORS = [
        'open'        => '#3496D7',
        'in_progress' => '#f6c23e',
        'on_hold'     => '#858796',
        'resolved'    => '#1cc88a',
        'closed'      => '#5a5c69',
    ];

    protected static function booted(): void
    {
        static::creating(function ($ticket) {
            $ticket->ticket_number = static::generateTicketNumber();
        });
    }

    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT-' . now()->format('Ymd');
        
        // Use a single optimized query with LIKE and ordering by ticket_id
        // Add index on ticket_number for faster LIKE queries
        $last = static::whereRaw("ticket_number LIKE ?", [$prefix . '%'])
                       ->orderByDesc('ticket_id')
                       ->value('ticket_number');

        $sequence = $last
            ? (int) substr($last, -5) + 1
            : 1;

        return $prefix . '-' . str_pad((string) $sequence, 5, '0', \STR_PAD_LEFT);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'created_by', 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'assigned_to', 'user_id');
    }

    public function externalClient(): BelongsTo
    {
        return $this->belongsTo(ExternalClient::class, 'external_client_id', 'id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(SysUser::class, 'ticket_assignees', 'ticket_id', 'user_id')
                    ->withPivot('assigned_by', 'assigned_at')
                    ->orderBy('user_surname');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'resolved_by', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'ticket_id')
                    ->orderBy('created_at');
    }

    public function publicComments(): HasMany
    {
        return $this->comments()->where('is_internal', 0);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id', 'ticket_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id', 'ticket_id')
                    ->orderByDesc('changed_at');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(TicketNotification::class, 'ticket_id', 'ticket_id');
    }

    public function getPriorityColorAttribute(): string
    {
        return self::PRIORITY_COLORS[$this->priority] ?? '#6c757d';
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? '#6c757d';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_at
            && $this->due_at->isPast()
            && !in_array($this->status, ['resolved', 'closed']);
    }

    public function getIsOpenAttribute(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'on_hold']);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'on_hold']);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_at')
                     ->where('due_at', '<', now())
                     ->whereNotIn('status', ['resolved', 'closed']);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhereHas('assignees', fn($a) => $a->where('ticket_assignees.user_id', $userId));
        });
    }

    public function scopeForDepartment($query, int $departmentId)
    {
        return $query->where('created_from_dept', $departmentId);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
