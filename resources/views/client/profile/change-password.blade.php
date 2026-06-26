@extends('layouts.client')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h4 mb-1" style="color:var(--brand-blue);">Change Password</h1>
        <p class="text-muted mb-0">Update your account security</p>
    </div>
    <a href="{{ route('client.profile') }}" class="btn btn-outline-secondary btn-sm">Back to Profile</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('client.profile.change-password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        <small class="text-muted">Must be at least 8 characters</small>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Change Password</button>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            <strong>Password Tips:</strong>
            <ul class="mb-0 mt-2">
                <li>Use a mix of uppercase and lowercase letters</li>
                <li>Include numbers and special characters</li>
                <li>Make it at least 8 characters long</li>
                <li>Avoid common words or personal information</li>
            </ul>
        </div>
    </div>
</div>

@endsection
