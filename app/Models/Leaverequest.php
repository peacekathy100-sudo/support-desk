<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\SysUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table      = 'leave_requests';
    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'leave_number',
        'user_id',
        'supervisor_id',
        'approved_by',
        'leave_type',
        'other_specify',
        'from_date',
        'to_date',
        'days_requested',
        'reason',
        'status',
        'rejection_reason',
        'approved_at',
        'attachment_path',
        'attachment_name',
    ];

    protected $casts = [
        'from_date'   => 'date',
        'to_date'     => 'date',
        'approved_at' => 'datetime',
    ];

    const LEAVE_TYPES = [
        'sick'                 => 'Sick',
        'bereavement'          => 'Bereavement',
        'time_off_without_pay' => 'Time Off Without Pay',
        'personal_annual'      => 'Personal / Annual Leave',
        'maternity_paternity'  => 'Maternity / Paternity',
        'other'                => 'Other',
    ];

    const STATUS_COLORS = [
        'pending'   => 'warning',
        'approved'  => 'success',
        'rejected'  => 'danger',
        'cancelled' => 'secondary',
    ];

    protected static function booted(): void
    {
        static::creating(function ($leave) {
            $leave->leave_number = static::generateLeaveNumber();
            if ($leave->from_date && $leave->to_date) {
                $leave->days_requested = \Carbon\Carbon::parse($leave->from_date)
                    ->diffInWeekdays(\Carbon\Carbon::parse($leave->to_date)) + 1;
            }
        });
    }

    public static function generateLeaveNumber(): string
    {
        $prefix = 'LV-' . now()->format('Ymd');
        $last   = static::where('leave_number', 'like', $prefix . '%')
                        ->orderByDesc('leave_id')
                        ->value('leave_number');
        $seq = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . '-' . str_pad((string) $seq, 4, '0', \STR_PAD_LEFT);
    }

    public function getLabelAttribute(): string
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? ucfirst($this->leave_type);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'user_id', 'user_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'supervisor_id', 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'approved_by', 'user_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
