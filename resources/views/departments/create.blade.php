@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Create Department</h1>
            <p class="text-muted mb-0">Add a new cost center or department.</p>
        </div>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary btn-sm">Back to departments</a>
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
            <form method="POST" action="{{ route('departments.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Department Name</label>
                        <input name="dept_name" value="{{ old('dept_name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department Code</label>
                        <input name="dept_code" value="{{ old('dept_code') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department Head</label>
                        <select name="dept_head" class="form-select">
                            <option value="">None</option>
                            @foreach($heads as $head)
                                <option value="{{ $head->user_id }}" {{ old('dept_head') == $head->user_id ? 'selected' : '' }}>{{ $head->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Active</label>
                        <select name="is_active" class="form-select">
                            <option value="1" selected>Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Save Department</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
