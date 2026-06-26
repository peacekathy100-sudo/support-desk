<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdmin;
use App\Models\Department;
use App\Models\SysUser;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EnsureAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_middleware_allows_redirect_responses(): void
    {
        $department = Department::create([
            'dept_name' => 'Support Operations',
            'dept_code' => 'SUP-OPS',
            'dept_head' => null,
            'is_active' => 1,
        ]);

        $role = UserRole::create([
            'ur_name' => 'Main Admin',
            'permissions' => ['*'],
            'is_active' => 1,
        ]);

        $admin = SysUser::factory()->create([
            'user_name' => 'sysadmin',
            'user_role' => $role->ur_id,
            'dept_id' => $department->dept_id,
        ]);

        Auth::login($admin);

        $response = app(EnsureAdmin::class)->handle(
            Request::create('/clients/1', 'PUT'),
            fn () => redirect()->route('clients.index')->with('success', 'Client updated successfully.')
        );

        $this->assertTrue($response->isRedirect());
        $this->assertSame(route('clients.index'), $response->headers->get('Location'));
    }
}
