<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\UserRole;
use App\Models\AuditTrail;
use App\Models\Department;
use App\Models\TicketNotification;
use App\Notifications\FlaxemResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SysUser extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table      = 'sysuser';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_name',
        'check_number',
        'user_surname',
        'user_othername',
        'user_email',
        'user_password',
        'user_telephone',
        'user_gender',
        'user_role',
        'dept_id',
        'client_id',
        'user_status',
        'profile_photo',
        'user_last_logged_in',
        'user_online',
    ];

    protected $hidden = ['user_password', 'remember_token'];

    protected $casts = [
        'user_last_logged_in' => 'datetime',
        'user_online'         => 'boolean',
        'is_system'           => 'boolean',
    ];

    public function getAuthPassword(): string
    {
        return $this->user_password;
    }

    public function getAuthIdentifierName(): string
    {
        return 'user_name';
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->user_email;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new FlaxemResetPasswordNotification($token));
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->user_surname . ' ' . $this->user_othername);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'user_role', 'ur_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function ticketsCreated(): HasMany
    {
        return $this->hasMany(Ticket::class, 'created_by', 'user_id');
    }

    public function ticketsAssigned(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to', 'user_id');
    }

    public function ticketNotifications(): HasMany
    {
        return $this->hasMany(TicketNotification::class, 'user_id', 'user_id')->latest();
    }

    public function unreadNotifications()
    {
        return $this->ticketNotifications()->where('is_read', 0);
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class, 'user_id', 'user_id');
    }

    public function chatParticipants(): MorphMany
    {
        return $this->morphMany(ConversationParticipant::class, 'participantable');
    }

    public function chatMessages(): MorphMany
    {
        return $this->morphMany(ChatMessage::class, 'sender');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role?->ur_name === $roleName;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperUser() && $this->isRestrictedPermission($permission) && !$this->isUserManagementPermission($permission)) {
            return false;
        }

        return $this->role?->hasPermission($permission) ?? false;
    }

    public function isClientRep(): bool
    {
        return !is_null($this->client_id);
    }

    public function isMainAdmin(): bool
    {
        $roleName = strtolower((string) $this->role?->ur_name);
        $userName = strtolower((string) $this->user_name);
        $otherName = strtolower((string) $this->user_othername);

        $namedMainAdmin = str_contains($roleName, 'main admin')
            || str_contains($roleName, 'administrator')
            || str_contains($userName, 'administrators')
            || str_contains($userName, 'sysadmin')
            || str_contains($userName, 'admin')
            || str_contains($otherName, 'main admin');

        return $namedMainAdmin && !$this->isSuperUser();
    }

    public function isSuperUser(): bool
    {
        $roleName = strtolower((string) $this->role?->ur_name);
        $userName = strtolower((string) $this->user_name);
        $otherName = strtolower((string) $this->user_othername);

        return str_contains($roleName, 'super user')
            || str_contains($roleName, 'super admin')
            || str_contains($userName, 'superadmin')
            || str_contains($userName, 'super user')
            || str_contains($otherName, 'super user')
            || str_contains($otherName, 'super admin');
    }

    public function isAdmin(): bool
    {
        if ($this->isMainAdmin()) {
            return true;
        }

        if ($this->isSuperUser()) {
            return false;
        }

        $permissions = $this->role?->permissions ?? [];
        return in_array('*', $permissions);
    }

    public function isAgent(): bool
    {
        $permissions = $this->role?->permissions ?? [];
        return in_array('*', $permissions)
            || in_array('tickets.*', $permissions)
            || in_array('edit_tickets', $permissions);
    }

    private function isUserManagementPermission(string $permission): bool
    {
        return in_array($permission, [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ], true);
    }

    private function isRestrictedPermission(string $permission): bool
    {
        return in_array($permission, [
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',
            'manage_settings',
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
        ], true);
    }

    public static function findMainAdmin(): ?self
    {
        return static::with('role')
            ->where('user_status', 'active')
            ->where(function ($query) {
                $query->where('user_name', 'like', '%admin%')
                    ->orWhere('user_othername', 'like', '%main admin%')
                    ->orWhereHas('role', function ($roleQuery) {
                        $roleQuery->where('ur_name', 'like', '%main admin%')
                            ->orWhere('ur_name', 'like', '%administrator%');
                    });
            })
            ->get()
            ->first(fn (self $user) => $user->isMainAdmin());
    }

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->check_number)) {
                $prefix = 'USR-' . now()->format('Ymd');
                $last = static::where('check_number', 'like', $prefix . '%')
                              ->orderByDesc('user_id')
                              ->value('check_number');

                $sequence = $last
                    ? (int) substr($last, strrpos($last, '-') + 1) + 1
                    : 1;

                $user->check_number = $prefix . '-' . str_pad((string) $sequence, 4, '0', \STR_PAD_LEFT);
            }
        });
    }
}
