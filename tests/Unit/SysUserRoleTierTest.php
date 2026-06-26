<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\SysUser;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SysUserRoleTierTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $roleName): SysUser
    {
        $department = Department::create([
            'dept_name' => 'Support Operations',
            'dept_code' => 'SUP-OPS',
            'dept_head' => null,
            'is_active' => 1,
        ]);

        $role = UserRole::create([
            'ur_name' => $roleName,
            'permissions' => ['tickets.*'],
            'is_active' => 1,
        ]);

        return SysUser::create([
            'user_name' => strtolower(str_replace(' ', '_', $roleName)) . '_test',
            'check_number' => 'CHK-' . uniqid(),
            'user_surname' => 'Test',
            'user_othername' => 'User',
            'user_email' => strtolower(str_replace(' ', '_', $roleName)) . '_test@example.com',
            'user_password' => bcrypt('password'),
            'user_telephone' => '0712345678',
            'user_gender' => 'Male',
            'user_role' => $role->ur_id,
            'dept_id' => $department->dept_id,
            'user_status' => 'active',
        ]);
    }

    public function test_main_admin_role_is_identified(): void
    {
        $user = $this->makeUser('Main Admin');

        $this->assertTrue($user->isMainAdmin());
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isSuperUser());
    }

    public function test_super_user_role_is_identified(): void
    {
        $user = $this->makeUser('Super User');

        $this->assertTrue($user->isSuperUser());
        $this->assertFalse($user->isMainAdmin());
        $this->assertFalse($user->isAdmin());
    }

    public function test_regular_user_is_not_privileged(): void
    {
        $user = $this->makeUser('User');

        $this->assertFalse($user->isMainAdmin());
        $this->assertFalse($user->isSuperUser());
        $this->assertFalse($user->isAdmin());
    }

    public function test_super_user_can_view_users(): void
    {
        $department = Department::create([
            'dept_name' => 'Support Operations',
            'dept_code' => 'SUP-OPS',
            'dept_head' => null,
            'is_active' => 1,
        ]);

        $role = UserRole::create([
            'ur_name' => 'Super User',
            'permissions' => ['view_users', 'tickets.*'],
            'is_active' => 1,
        ]);

        $user = SysUser::create([
            'user_name' => 'super_user_test',
            'check_number' => 'CHK-' . uniqid(),
            'user_surname' => 'Test',
            'user_othername' => 'User',
            'user_email' => 'super_user_test@example.com',
            'user_password' => bcrypt('password'),
            'user_telephone' => '0712345678',
            'user_gender' => 'Male',
            'user_role' => $role->ur_id,
            'dept_id' => $department->dept_id,
            'user_status' => 'active',
        ]);

        $this->assertTrue($user->isSuperUser());
        $this->assertTrue($user->hasPermission('view_users'));
    }
}
