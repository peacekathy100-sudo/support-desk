<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SysUser;
use App\Models\UserRole;
use Illuminate\View\View;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\UserCreatedMail;
use App\Mail\UserTerminatedMail;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct(private AuditService $auditor)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $this->gate('view_users');
        $users = SysUser::with(['role', 'department'])
                        ->orderBy('user_surname')
                        ->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->gate('create_users');
        $roles       = UserRole::where('is_active', 1)->get();
        $departments = Department::where('is_active', 1)->get();
        $clients     = Client::where('is_active', 1)->orderBy('client_name')->get();
        return view('users.create', compact('roles', 'departments', 'clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->gate('create_users');

        $data = $request->validate([
            'user_name'      => 'required|string|unique:sysuser,user_name|max:50',
            'check_number'   => 'nullable|string|unique:sysuser,check_number|max:50',
            'user_surname'   => 'required|string|max:100',
            'user_othername' => 'nullable|string|max:100',
            'user_email'     => 'required|email|unique:sysuser,user_email',
            'user_telephone' => 'nullable|string|max:20',
            'user_gender'    => 'nullable|in:Male,Female,Other',
            'user_role'      => 'required|exists:user_roles,ur_id',
            'dept_id'        => 'required|exists:departments,dept_id',
            'client_id'      => 'nullable|exists:clients,client_id',
            'user_status'    => 'required|in:active,inactive,suspended',
        ]);

        $plainPassword      = Str::random(10);
        $data['user_password'] = Hash::make($plainPassword);

        $user = SysUser::create($data);

        try {
            Mail::to($user->user_email)->send(new UserCreatedMail($user, $plainPassword));
        } catch (\Exception $e) {
            \Log::warning("Could not send welcome email to {$user->user_email}: " . $e->getMessage());
        }

        $this->auditor->log('created', 'SysUser', $user->user_id, null, [
            'user_name' => $user->user_name,
            'user_email' => $user->user_email,
        ]);

        return redirect()->route('users.index')
                         ->with('success', "User {$user->user_name} created. Login credentials sent to {$user->user_email}.");
    }

    public function edit(SysUser $user): View
    {
        $this->gate('edit_users');
        $roles       = UserRole::where('is_active', 1)->get();
        $departments = Department::where('is_active', 1)->get();
        $clients     = Client::where('is_active', 1)->orderBy('client_name')->get();
        return view('users.edit', compact('user', 'roles', 'departments', 'clients'));
    }

    public function update(Request $request, SysUser $user): RedirectResponse
    {
        $this->gate('edit_users');

        $data = $request->validate([
            'user_surname'   => 'required|string|max:100',
            'user_othername' => 'nullable|string|max:100',
            'user_email'     => 'required|email|unique:sysuser,user_email,' . $user->user_id . ',user_id',
            'user_telephone' => 'nullable|string|max:20',
            'user_gender'    => 'nullable|in:Male,Female,Other',
            'user_role'      => 'required|exists:user_roles,ur_id',
            'dept_id'        => 'required|exists:departments,dept_id',
            'client_id'      => 'nullable|exists:clients,client_id',
            'user_status'    => 'required|in:active,inactive,suspended',
        ]);

        $old = $user->toArray();
        $user->update($data);

        $this->auditor->log('updated', 'SysUser', $user->user_id, $old, $data);

        return redirect()->route('users.index')
                         ->with('success', "User {$user->user_name} updated.");
    }

    public function destroy(SysUser $user): RedirectResponse
    {
        $this->gate('delete_users');

        if ($user->is_system || $user->user_name === 'administrators' || $user->user_name === 'admin') {
            return back()->with('error', 'System users cannot be deleted.');
        }

        $this->auditor->log('deleted', 'SysUser', $user->user_id, $user->toArray(), null);

        try {
            Mail::to($user->user_email)->send(new UserTerminatedMail($user));
        } catch (\Exception $e) {
            Log::warning('User termination email failed: ' . $e->getMessage());
        }

        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User deleted successfully.');
    }

    public function purge(string $user): RedirectResponse
    {
        $this->gate('delete_users');

        $user = SysUser::withTrashed()->findOrFail($user);

        if (!$user->trashed()) {
            return back()->with('error', 'Only soft-deleted users can be purged. Delete the user first.');
        }

        $this->auditor->log('purged', 'SysUser', $user->user_id, $user->toArray(), null);
        $this->scrubUserReferences($user->user_id);
        $user->forceDelete();

        return redirect()->route('users.index')
                         ->with('success', "User {$user->user_name} has been permanently removed.");
    }

    public function purgeAll(): RedirectResponse
    {
        $this->gate('delete_users');

        $trashed = SysUser::onlyTrashed()->get();

        if ($trashed->isEmpty()) {
            return back()->with('error', 'There are no deleted users to purge.');
        }

        DB::transaction(function () use ($trashed) {
            foreach ($trashed as $user) {
                $this->auditor->log('purged', 'SysUser', $user->user_id, $user->toArray(), null);
                $this->scrubUserReferences($user->user_id);
                $user->forceDelete();
            }
        });

        return redirect()->route('users.index')
                         ->with('success', "{$trashed->count()} deleted user(s) permanently removed from the database.");
    }

    private function scrubUserReferences(int $userId): void
    {
        DB::table('leave_requests')->where('user_id', $userId)->delete();
        DB::table('leave_requests')->where('supervisor_id', $userId)->update(['supervisor_id' => null]);
        DB::table('leave_requests')->where('approved_by',   $userId)->update(['approved_by'   => null]);

        $ticketIds = DB::table('tickets')->where('created_by', $userId)->pluck('ticket_id');
        if ($ticketIds->isNotEmpty()) {
            DB::table('ticket_history')->whereIn('ticket_id', $ticketIds)->delete();
            DB::table('ticket_comments')->whereIn('ticket_id', $ticketIds)->delete();
            DB::table('ticket_attachments')->whereIn('ticket_id', $ticketIds)->delete();
            DB::table('ticket_notifications')->whereIn('ticket_id', $ticketIds)->delete();
            DB::table('tickets')->whereIn('ticket_id', $ticketIds)->delete();
        }
        DB::table('tickets')->where('assigned_to', $userId)->update(['assigned_to' => null]);
        DB::table('tickets')->where('resolved_by', $userId)->update(['resolved_by' => null]);
        DB::table('tickets')->where('reopened_by', $userId)->update(['reopened_by' => null]);

        DB::table('ticket_comments')->where('user_id', $userId)->delete();
        DB::table('ticket_attachments')->where('uploaded_by', $userId)->delete();
        DB::table('ticket_history')->where('changed_by', $userId)->delete();
        DB::table('ticket_notifications')->where('user_id', $userId)->delete();

        DB::table('audit_trails')->where('user_id', $userId)->update(['user_id' => null]);
    }

    public function resetPassword(SysUser $user): RedirectResponse
    {
        $this->gate('edit_users');

        $plainPassword = Str::random(10);
        $user->update(['user_password' => Hash::make($plainPassword)]);

        try {
            Mail::to($user->user_email)->send(new UserCreatedMail($user, $plainPassword));
        } catch (\Exception $e) {
            \Log::warning("Password reset email failed: " . $e->getMessage());
        }

        $this->auditor->log('password_reset', 'SysUser', $user->user_id);

        return back()->with('success', "Password reset. New credentials sent to {$user->user_email}.");
    }
}
