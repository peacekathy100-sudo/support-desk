<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Ticket;
use App\Models\SysUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'dept_id';

    protected $fillable = [
        'dept_name',
        'dept_code',
        'dept_head',
        'is_active',
    ];

    public function head(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'dept_head', 'user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(SysUser::class, 'dept_id', 'dept_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'created_from_dept', 'dept_id');
    }
}
