<?php

namespace Tests\Feature;

use App\Mail\UserTerminatedMail;
use App\Models\Department;
use App\Models\SysUser;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserDeletionNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_user_soft_deletes_it_and_sends_a_termination_email(): void
    {
        Mail::fake();

        $adminRole = UserRole::factory()->admin()->create([
            'ur_name' => 'Main Admin',
        ]);
        $department = Department::create([
            'dept_name' => 'Support Operations',
            'dept_code' => 'SUP-001',
            'is_active' => 1,
        ]);

        $admin = SysUser::factory()->create([
            'user_name' => 'administrators',
            'user_othername' => 'Main Admin',
            'user_role' => $adminRole->ur_id,
            'dept_id' => $department->dept_id,
        ]);

        $target = SysUser::factory()->create([
            'user_role' => $adminRole->ur_id,
            'dept_id' => $department->dept_id,
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('users.destroy', $target));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('sysuser', ['user_id' => $target->user_id]);

        $usersPage = $this->get(route('users.index'));
        $usersPage->assertOk();
        $usersPage->assertDontSee($target->full_name);
        $usersPage->assertDontSee($target->user_email);

        Mail::assertSent(UserTerminatedMail::class, function (UserTerminatedMail $mail) use ($target) {
            return $mail->hasTo($target->user_email)
                && $mail->user->is($target);
        });
    }
}
