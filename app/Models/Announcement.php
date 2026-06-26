<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'status',
        'priority',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    const TYPES = ['maintenance', 'feature', 'general'];
    const STATUSES = ['draft', 'published', 'archived'];
    const PRIORITIES = ['low', 'medium', 'high'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'created_by', 'user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'published' 
            && (!$this->start_date || now()->isAfter($this->start_date))
            && (!$this->end_date || now()->isBefore($this->end_date));
    }
}
