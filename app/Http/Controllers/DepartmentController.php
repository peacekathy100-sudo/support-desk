<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SysUser;
use Illuminate\View\View;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\AuditService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DepartmentController extends Controller
{
    public function __construct(private AuditService $auditor)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $this->gate('view_departments');
        $departments = Department::with(['head', 'users'])->get();
        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        $this->gate('create_departments');
        $heads = SysUser::where('user_status', 'active')->orderBy('user_surname')->get();
        return view('departments.create', compact('heads'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->gate('create_departments');
        $data = $request->validate([
            'dept_name'  => 'required|string|max:100',
            'dept_code'  => 'required|string|max:20|unique:departments,dept_code',
            'dept_head'  => 'nullable|exists:sysuser,user_id',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $dept = Department::create($data);

        $this->auditor->log('created', 'Department', $dept->dept_id, null, $data);

        return redirect()->route('departments.index')
                         ->with('success', "Department '{$dept->dept_name}' created.");
    }

    public function edit(Department $department): View
    {
        $this->gate('edit_departments');
        $heads = SysUser::where('user_status', 'active')->orderBy('user_surname')->get();
        return view('departments.edit', compact('department', 'heads'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $this->gate('edit_departments');
        $data = $request->validate([
            'dept_name' => 'required|string|max:100',
            'dept_code' => 'required|string|max:20|unique:departments,dept_code,' . $department->dept_id . ',dept_id',
            'dept_head' => 'nullable|exists:sysuser,user_id',
            'is_active' => 'nullable|boolean',
        ]);

        $old = $department->toArray();
        $data['is_active'] = $request->boolean('is_active', true);
        $department->update($data);

        $this->auditor->log('updated', 'Department', $department->dept_id, $old, $data);

        return redirect()->route('departments.index')
                         ->with('success', 'Department updated.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $this->gate('delete_departments');
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Cannot delete department with active users.');
        }

        $this->auditor->log('deleted', 'Department', $department->dept_id, $department->toArray(), null);
        $department->delete();

        return redirect()->route('departments.index')
                         ->with('success', 'Department deleted.');
    }
}
