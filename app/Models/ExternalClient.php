<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * External Client / Portal User Model
 * Represents customers/clients who can log into the client portal
 * and submit tickets
 */
class ExternalClient extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'external_clients';

    protected $fillable = [
        'company_name',
        'full_name',
        'email',
        'phone',
        'username',
        'password',
        'assigned_to_user_id',
        'category',
        'status',
        'notes',
        'created_by',
        'last_login_at',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the assigned company representative (SysUser)
     */
    public function assignedRepresentative(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'assigned_to_user_id', 'user_id');
    }

    /**
     * Get the admin who created this client
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'created_by', 'user_id');
    }

    /**
     * Get the synced CRM Client record
     */
    public function crmClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'email', 'client_email');
    }

    /**
     * Get all tickets created by this client
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'external_client_id', 'id');
    }

    public function chatParticipants(): MorphMany
    {
        return $this->morphMany(ConversationParticipant::class, 'participantable');
    }

    public function chatMessages(): MorphMany
    {
        return $this->morphMany(ChatMessage::class, 'sender');
    }

    /**
     * Check if client is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if client is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Get auth identifier name
     */
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    /**
     * Get auth password column
     */
    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Update last activity timestamp
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Unread client messages count.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->messages()->where('is_read', false)->count();
    }

    /**
     * Get all client messages.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'external_client_id', 'id');
    }

    /**
     * Get attachments from client's tickets
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'client_id', 'id');
    }

    /**
     * Get active tickets for this client
     */
    public function activeTickets()
    {
        return $this->tickets()
            ->whereNotIn('status', ['closed', 'resolved'])
            ->latest('updated_at');
    }

    /**
     * Get resolved tickets for this client
     */
    public function resolvedTickets()
    {
        return $this->tickets()
            ->whereIn('status', ['closed', 'resolved'])
            ->latest('resolved_at');
    }

    /**
     * Get ticket statistics for this client
     */
    public function getTicketStatistics(): array
    {
        return [
            'total' => $this->tickets()->count(),
            'open' => $this->tickets()->whereNotIn('status', ['closed', 'resolved'])->count(),
            'resolved' => $this->tickets()->whereIn('status', ['closed', 'resolved'])->count(),
            'urgent' => $this->tickets()->where('priority', 'urgent')->count(),
        ];
    }

    /**
     * Get assigned representative info
     */
    public function getRepresentativeInfo(): ?array
    {
        if (!$this->assignedRepresentative) {
            return null;
        }

        return [
            'name' => $this->assignedRepresentative->user_name . ' ' . $this->assignedRepresentative->user_surname,
            'email' => $this->assignedRepresentative->user_email,
            'phone' => $this->assignedRepresentative->user_telephone,
        ];
    }

    /**
     * Route notifications to email
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    /**
     * Get the displayable name of the model.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name . ' (' . $this->company_name . ')';
    }

    /**
     * Scope to active clients
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to assigned to a specific representative
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to_user_id', $userId);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
