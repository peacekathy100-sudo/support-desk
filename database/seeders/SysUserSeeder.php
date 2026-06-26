<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SysUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'Main Admin'],
            [
                'permissions' => json_encode(['*']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        $mainAdminRoleId = DB::table('user_roles')->where('ur_name', 'Main Admin')->value('ur_id');

        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'Super User'],
            [
                'permissions' => json_encode(['*']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'Operations Manager'],
            [
                'permissions' => json_encode(['tickets.*', 'view_users', 'view_departments', 'view_clients']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'Support Agent'],
            [
                'permissions' => json_encode(['tickets.*', 'view_users']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'Marketing Manager'],
            [
                'permissions' => json_encode(['tickets.*', 'view_clients', 'view_users']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'Finance Officer'],
            [
                'permissions' => json_encode(['tickets.view', 'view_clients']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'Client Representative'],
            [
                'permissions' => json_encode(['tickets.own']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        DB::table('user_roles')->updateOrInsert(
            ['ur_name' => 'User'],
            [
                'permissions' => json_encode(['tickets.own']),
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        $departments = [
            ['dept_code' => 'hr', 'dept_name' => 'HR'],
            ['dept_code' => 'support-ops', 'dept_name' => 'Support Operations'],
            ['dept_code' => 'marketing', 'dept_name' => 'Marketing'],
            ['dept_code' => 'sales', 'dept_name' => 'Sales'],
            ['dept_code' => 'finance', 'dept_name' => 'Finance'],
            ['dept_code' => 'it', 'dept_name' => 'IT'],
            ['dept_code' => 'operations', 'dept_name' => 'Operations'],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->updateOrInsert(
                ['dept_code' => $department['dept_code']],
                [
                    'dept_name'  => $department['dept_name'],
                    'is_active'  => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $supportDeptId = DB::table('departments')->where('dept_code', 'support-ops')->value('dept_id');

        DB::table('sysuser')->updateOrInsert(
            ['user_name' => 'administrators'],
            [
                'user_email'     => 'omarajeremiah23@gmail.com',
                'check_number'   => 'ADM-SYS-001',
                'user_surname'   => 'Administrators',
                'user_othername' => 'Main Admin',
                'user_password'  => Hash::make('123'),
                'user_role'      => $mainAdminRoleId,
                'dept_id'        => $supportDeptId,
                'user_status'    => 'active',
                'user_gender'    => 'Male',
                'deleted_at'     => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        );

        try {
            DB::table('sysuser')->where('user_name', 'administrators')->update(['is_system' => true]);
        } catch (\Exception $e) {
            // Column doesn't exist yet, will be added by migration
        }

        DB::table('sysuser')->updateOrInsert(
            ['user_name' => 'admin'],
            [
                'user_email'     => 'oasysglobal90@gmail.com',
                'check_number'   => 'ADM012',
                'user_surname'   => 'Adm',
                'user_othername' => 'Admin',
                'user_password'  => Hash::make('123'),
                'user_role'      => $mainAdminRoleId,
                'dept_id'        => $supportDeptId,
                'user_status'    => 'active',
                'user_gender'    => 'Male',
                'deleted_at'     => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        );

        try {
            DB::table('sysuser')->where('user_name', 'admin')->update(['is_system' => true]);
        } catch (\Exception $e) {
            // Column doesn't exist yet, will be added by migration
        }

        $superUserRoleId = DB::table('user_roles')->where('ur_name', 'Super User')->value('ur_id');

        DB::table('sysuser')->updateOrInsert(
            ['user_name' => 'sysadmin'],
            [
                'user_email'     => 'starxtrail771@gmail.com',
                'check_number'   => 'ADM001',
                'user_surname'   => 'System',
                'user_othername' => 'Administrator',
                'user_password'  => Hash::make('admin123'),
                'user_role'      => $superUserRoleId,
                'dept_id'        => $supportDeptId,
                'user_status'    => 'active',
                'user_gender'    => 'Male',
                'deleted_at'     => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        );

        try {
            DB::table('sysuser')->where('user_name', 'sysadmin')->update(['is_system' => true]);
        } catch (\Exception $e) {
            // Column doesn't exist yet, will be added by migration
        }

        DB::table('sysuser')->updateOrInsert(
            ['user_name' => 'superadmin'],
            [
                'user_email'     => 'omarajeremiah8@gmail.com',
                'check_number'   => 'ADM002',
                'user_surname'   => 'Super',
                'user_othername' => 'Admin',
                'user_password'  => Hash::make('super123'),
                'user_role'      => $superUserRoleId,
                'dept_id'        => $supportDeptId,
                'user_status'    => 'active',
                'user_gender'    => 'Male',
                'deleted_at'     => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        );

        try {
            DB::table('sysuser')->where('user_name', 'superadmin')->update(['is_system' => true]);
        } catch (\Exception $e) {
            // Column doesn't exist yet, will be added by migration
        }

        $this->command->info('Role and department defaults updated successfully.');
        $this->command->info('  - Main Admin is now the top-level role');
        $this->command->info('  - Added departments: Marketing, Sales, Finance, IT, Operations');
        $this->command->info('  - Existing admin accounts are mapped to the new role hierarchy.');
    }
}
