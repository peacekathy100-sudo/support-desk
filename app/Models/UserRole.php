<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\SysUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $table      = 'user_roles';
    protected $primaryKey = 'ur_id';

    protected $fillable = [
        'ur_name',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(SysUser::class, 'user_role', 'ur_id');
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        if (in_array('*', $permissions)) return true;

        if (in_array($permission, $permissions)) return true;

        foreach ($permissions as $p) {
            if (str_ends_with($p, '.*')) {
                $namespace = str_replace('.*', '', $p);
                if (str_starts_with($permission, $namespace . '_') ||
                    str_starts_with($permission, $namespace . '.')) {
                    return true;
                }
            }
        }

        return false;
    }
}
