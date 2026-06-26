@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Leave Requests</h1>
            <p class="text-muted mb-0">Track pending and approved leave requests.</p>
        </div>
        <a href="{{ route('leaves.create') }}" class="btn" style="background:var(--brand-blue); color:#fff;">Request Leave</a>
    </div>

    <div class="card shadow-sm mb-4" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('leaves.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input name="search" value="{{ request('search') }}" type="search" class="form-control form-control-sm" placeholder="Leave number or employee">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled'] as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($leaveTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.92rem;">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th>Leave #</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                        <tr>
                            <td>{{ $leave->leave_number }}</td>
                            <td>{{ $leave->employee?->full_name ?? '—' }}</td>
                            <td>{{ $leave->leave_type }}</td>
                            <td>{{ $leave->from_date->format('M j, Y') }} – {{ $leave->to_date->format('M j, Y') }}</td>
                            <td><span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : ($leave->status === 'cancelled' ? 'secondary' : 'warning')) }}">{{ ucfirst($leave->status) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No leave requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $leaves->links() }}</div>
        </div>
    </div>
</div>
@endsection
