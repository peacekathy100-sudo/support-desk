<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\AuditService;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UserRoleController extends Controller
{
    public function __construct(private AuditService $auditor)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $this->gate('view_roles');

        $roles = UserRole::withCount('users')
            ->orderByRaw("CASE
                WHEN ur_name = 'Main Admin' THEN 0
                WHEN ur_name = 'Super User' THEN 1
                WHEN ur_name = 'Operations Manager' THEN 2
                WHEN ur_name = 'Marketing Manager' THEN 3
                WHEN ur_name = 'Finance Officer' THEN 4
                WHEN ur_name = 'Support Agent' THEN 5
                WHEN ur_name = 'Client Representative' THEN 6
                ELSE 7
            END")
            ->orderBy('ur_name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->gate('create_roles');
        return view('roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->gate('create_roles');
        $data = $request->validate([
            'ur_name'     => 'required|string|max:100|unique:user_roles,ur_name',
            'permissions' => 'nullable',
            'is_active'   => 'nullable|boolean',
        ]);

        if (is_string($data['permissions'] ?? null)) {
            $data['permissions'] = preg_split('/\s*,\s*/', trim($data['permissions']), -1, PREG_SPLIT_NO_EMPTY);
        }

        $data['is_active']   = $request->boolean('is_active', true);
        $data['permissions'] = $data['permissions'] ?? [];
        $role = UserRole::create($data);

        $this->auditor->log('created', 'UserRole', $role->ur_id, null, $data);

        return redirect()->route('roles.index')
                         ->with('success', "Role '{$role->ur_name}' created.");
    }

    public function edit(UserRole $role): View
    {
        $this->gate('edit_roles');
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, UserRole $role): RedirectResponse
    {
        $this->gate('edit_roles');
        $data = $request->validate([
            'ur_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('user_roles', 'ur_name')->ignore($role->ur_id, 'ur_id'),
            ],
            'permissions' => 'nullable',
            'is_active'   => 'nullable|boolean',
        ]);

        if (is_string($data['permissions'] ?? null)) {
            $data['permissions'] = preg_split('/\s*,\s*/', trim($data['permissions']), -1, PREG_SPLIT_NO_EMPTY);
        }

        $old = $role->toArray();
        $data['is_active']   = $request->boolean('is_active', true);
        $data['permissions'] = $data['permissions'] ?? [];
        $role->update($data);

        $this->auditor->log('updated', 'UserRole', $role->ur_id, $old, $data);

        return redirect()->route('roles.index')
                         ->with('success', 'Role updated successfully.');
    }

    public function destroy(UserRole $role): RedirectResponse
    {
        $this->gate('delete_roles');
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that has users assigned.');
        }

        $this->auditor->log('deleted', 'UserRole', $role->ur_id, $role->toArray(), null);
        $role->delete();

        return redirect()->route('roles.index')
                         ->with('success', 'Role deleted.');
    }
}
