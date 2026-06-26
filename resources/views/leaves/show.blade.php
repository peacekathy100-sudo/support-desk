@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Leave request {{ $leave->leave_number }}</h1>
            <p class="text-muted mb-0">Review the leave request details and approval status.</p>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
    </div>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Employee</div>
                            <div>{{ $leave->employee?->full_name ?? '�' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Type</div>
                            <div>{{ $leave->leave_type }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Period</div>
                            <div>{{ $leave->from_date->format('M j, Y') }} to {{ $leave->to_date->format('M j, Y') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Duration</div>
                            <div>{{ $leave->days_requested }} day(s)</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted" style="font-size:0.82rem;">Reason</div>
                        <div>{{ $leave->reason }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted" style="font-size:0.82rem;">Supervisor</div>
                        <div>{{ $leave->supervisor?->full_name ?? 'Not assigned' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted" style="font-size:0.82rem;">Approver</div>
                        <div>{{ $leave->approver?->full_name ?? 'Pending' }}</div>
                    </div>
                    @if($leave->attachment_path)
                        <div class="mb-3">
                            <a href="{{ asset('storage/' . $leave->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Download attachment</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted" style="font-size:0.82rem;">Status</div>
                        <span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : ($leave->status === 'cancelled' ? 'secondary' : 'warning')) }}">{{ ucfirst($leave->status) }}</span>
                    </div>
                    @if($leave->rejection_reason)
                        <div class="mb-2">
                            <div class="text-muted" style="font-size:0.82rem;">Rejection reason</div>
                            <div>{{ $leave->rejection_reason }}</div>
                        </div>
                    @endif
                    <div class="mb-2">
                        <div class="text-muted" style="font-size:0.82rem;">Submitted</div>
                        <div>{{ $leave->created_at?->format('M j, Y H:i') }}</div>
                    </div>
                    @if(auth()->user()->isMainAdmin() && $leave->status === 'pending')
                        <form method="POST" action="{{ route('leaves.approve', $leave) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success w-100">Approve</button>
                        </form>
                        <button class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="collapse" data-bs-target="#rejectForm">Reject</button>
                        <div class="collapse mt-2" id="rejectForm">
                            <form method="POST" action="{{ route('leaves.reject', $leave) }}">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" placeholder="Reason for rejection" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-danger w-100">Submit rejection</button>
                            </form>
                        </div>
                    @endif
                    @if(auth()->user()->user_id === $leave->user_id && $leave->status === 'pending')
                        <form method="POST" action="{{ route('leaves.cancel', $leave) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Cancel request</button>
                        </form>
                    @endif
                    <a href="{{ route('leaves.print', $leave) }}" class="btn btn-sm btn-outline-primary w-100 mt-2" target="_blank">
                        <i class="ph-printer"></i> Print Document
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
