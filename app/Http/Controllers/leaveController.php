<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SysUser;
use Illuminate\View\View;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $user  = auth()->user();
        $query = LeaveRequest::with(['employee', 'supervisor', 'approver']);

        // Only MainAdmin can see all leave requests
        // Regular users can only see their own
        if (!$user->isMainAdmin()) {
            $query->forUser($user->user_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('leave_type', $request->type);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('leave_number', 'like', "%{$request->search}%")
                  ->orWhereHas('employee', fn($eq) =>
                      $eq->where('user_surname', 'like', "%{$request->search}%")
                         ->orWhere('user_othername', 'like', "%{$request->search}%")
                  );
            });
        }

        $leaves     = $query->latest()->paginate(20);
        $leaveTypes = LeaveRequest::LEAVE_TYPES;

        return view('leaves.index', compact('leaves', 'leaveTypes'));
    }

    public function create(): View
    {
        $supervisors = SysUser::where('user_status', 'active')
                              ->where('user_id', '!=', auth()->user()->user_id)
                              ->orderBy('user_surname')
                              ->get();

        $leaveTypes = LeaveRequest::LEAVE_TYPES;

        return view('leaves.create', compact('supervisors', 'leaveTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'leave_type'    => 'required|in:' . implode(',', array_keys(LeaveRequest::LEAVE_TYPES)),
            'other_specify' => 'nullable|required_if:leave_type,other|string|max:255',
            'supervisor_id' => 'nullable|exists:sysuser,user_id',
            'from_date'     => 'required|date|after_or_equal:today',
            'to_date'       => 'required|date|after_or_equal:from_date',
            'reason'        => 'required|string|min:10',
            'attachment'    => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $data['user_id'] = auth()->user()->user_id;
        $data['status']  = 'pending';

        if ($request->hasFile('attachment')) {
            $file                   = $request->file('attachment');
            $data['attachment_path'] = $file->store('leave_attachments', 'public');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $leave = LeaveRequest::create($data);

        return redirect()->route('leaves.show', $leave->leave_id)
                         ->with('success', "Leave request {$leave->leave_number} submitted successfully.");
    }

    public function show(LeaveRequest $leave): View
    {
        $this->authorizeView($leave);
        $leave->load(['employee', 'supervisor', 'approver']);
        return view('leaves.show', compact('leave'));
    }

    public function approve(LeaveRequest $leave): RedirectResponse
    {
        $this->authorizeMainAdmin();

        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->user()->user_id,
            'approved_at' => now(),
        ]);

        return back()->with('success', "Leave request {$leave->leave_number} approved.");
    }

    public function reject(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $this->authorizeMainAdmin();

        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $leave->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "Leave request {$leave->leave_number} rejected.");
    }

    public function cancel(LeaveRequest $leave): RedirectResponse
    {
        if ($leave->user_id !== auth()->user()->user_id) {
            abort(403);
        }

        if (!in_array($leave->status, ['pending'])) {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);

        return redirect()->route('leaves.index')
                         ->with('success', "Leave request {$leave->leave_number} cancelled.");
    }

    public function print(LeaveRequest $leave): \Illuminate\Http\Response
    {
        $this->authorizeView($leave);
        $leave->load(['employee', 'supervisor', 'approver']);
        
        $html = view('leaves.print', compact('leave'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=leave-{$leave->leave_number}.html");
    }

    public function destroy(LeaveRequest $leave): RedirectResponse
    {
        $this->authorizeMainAdmin();

        if ($leave->attachment_path) {
            Storage::disk('public')->delete($leave->attachment_path);
        }

        $leave->delete();

        return redirect()->route('leaves.index')
                         ->with('success', 'Leave request deleted.');
    }

    private function authorizeView(LeaveRequest $leave): void
    {
        $user = auth()->user();
        if (!$user->isMainAdmin() && $leave->user_id !== $user->user_id) {
            abort(403, 'You do not have access to this leave request.');
        }
    }

    private function authorizeMainAdmin(): void
    {
        if (!auth()->user()->isMainAdmin()) {
            abort(403, 'Only Main Admin can perform this action.');
        }
    }
}
