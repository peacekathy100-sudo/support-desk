@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Create Role</h1>
            <p class="text-muted mb-0">Add a new role for your support employees.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">Back to roles</a>
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
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Role name</label>
                        <input name="ur_name" value="{{ old('ur_name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Active</label>
                        <select name="is_active" class="form-select">
                            <option value="1" selected>Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Permissions</label>
                        <textarea name="permissions" rows="4" class="form-control" placeholder='Enter JSON array or comma-separated values'>{{ old('permissions') }}</textarea>
                        <div class="form-text">Example: ["tickets.*","view_reports"] or tickets.*,view_reports</div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Save Role</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
