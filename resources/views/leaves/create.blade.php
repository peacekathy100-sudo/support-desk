@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Request Leave</h1>
            <p class="text-muted mb-0">Submit a new leave request for approval.</p>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary btn-sm">Back to leave list</a>
    </div>
    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('leaves.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Leave type</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="">Select type</option>
                            @foreach($leaveTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('leave_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Supervisor</label>
                        <select name="supervisor_id" class="form-select">
                            <option value="">No supervisor</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->user_id }}" {{ old('supervisor_id') == $supervisor->user_id ? 'selected' : '' }}>{{ $supervisor->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">From date</label>
                        <input name="from_date" type="date" value="{{ old('from_date') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">To date</label>
                        <input name="to_date" type="date" value="{{ old('to_date') }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" rows="4" class="form-control" required>{{ old('reason') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Attachment</label>
                        <input name="attachment" type="file" class="form-control">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Submit Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
