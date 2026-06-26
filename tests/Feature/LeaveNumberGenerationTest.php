<?php

namespace Tests\Feature;

use App\Models\Leaverequest;
use App\Models\SysUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeaveNumberGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_leave_numbers_are_generated_as_zero_padded_strings(): void
    {
        $roleId = DB::table('user_roles')->insertGetId([
            'ur_name' => 'Support Agent',
            'permissions' => json_encode(['tickets.*']),
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $departmentId = DB::table('departments')->insertGetId([
            'dept_name' => 'Human Resources',
            'dept_code' => 'hr',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        SysUser::create([
            'user_name' => 'leave-tester',
            'check_number' => 'CHK-001',
            'user_surname' => 'Tester',
            'user_othername' => 'Leave',
            'user_email' => 'leave-tester@example.com',
            'user_password' => bcrypt('secret'),
            'user_role' => $roleId,
            'dept_id' => $departmentId,
            'user_status' => 'active',
            'user_gender' => 'Male',
        ]);

        $firstLeave = Leaverequest::create([
            'user_id' => 1,
            'supervisor_id' => 1,
            'leave_type' => 'sick',
            'from_date' => now()->toDateString(),
            'to_date' => now()->addDays(2)->toDateString(),
            'reason' => 'Medical leave',
        ]);

        $secondLeave = Leaverequest::create([
            'user_id' => 1,
            'supervisor_id' => 1,
            'leave_type' => 'personal_annual',
            'from_date' => now()->addDays(3)->toDateString(),
            'to_date' => now()->addDays(5)->toDateString(),
            'reason' => 'Annual leave',
        ]);

        $prefix = 'LV-' . now()->format('Ymd');

        $this->assertSame($prefix . '-0001', $firstLeave->leave_number);
        $this->assertSame($prefix . '-0002', $secondLeave->leave_number);
    }
}
