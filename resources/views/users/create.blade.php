@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Create User</h1>
            <p class="text-muted mb-0">Add a new team member to the support system.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
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
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Username</label>
                        <input name="user_name" value="{{ old('user_name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Surname</label>
                        <input name="user_surname" value="{{ old('user_surname') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Other name</label>
                        <input name="user_othername" value="{{ old('user_othername') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input name="user_email" type="email" value="{{ old('user_email') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Telephone</label>
                        <input name="user_telephone" value="{{ old('user_telephone') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="user_gender" class="form-select">
                            <option value="">Select</option>
                            <option value="Male" {{ old('user_gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('user_gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('user_gender') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role</label>
                        <select name="user_role" class="form-select" required>
                            <option value="">Select role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->ur_id }}" {{ old('user_role') == $role->ur_id ? 'selected' : '' }}>{{ $role->ur_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <select name="dept_id" class="form-select" required>
                            <option value="">Select department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->dept_id }}" {{ old('dept_id') == $department->dept_id ? 'selected' : '' }}>{{ $department->dept_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Client</label>
                        <select name="client_id" class="form-select">
                            <option value="">No client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->client_id }}" {{ old('client_id') == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }} ({{ $client->client_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="user_status" class="form-select" required>
                            <option value="active" {{ old('user_status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('user_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('user_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Save User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
