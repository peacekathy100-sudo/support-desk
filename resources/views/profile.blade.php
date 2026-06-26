@extends('layouts.master')

@section('content')
@php
    $user = auth()->user();
    $avatar = $user->profile_photo
        ? asset('images/' . $user->profile_photo)
        : URL::asset('assets/images/centenary.png');
    $statusClass = $user->user_status === 'active' ? 'success' : 'secondary';
@endphp

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $avatar }}" alt="Profile photo" class="rounded-circle border" width="92" height="92" style="object-fit: cover;">
                        <div>
                            <h1 class="h5 mb-1" style="color:var(--brand-blue);">{{ $user->full_name ?: $user->user_name }}</h1>
                            <p class="mb-1 text-muted">{{ $user->role?->ur_name ?? 'User' }}</p>
                            <span class="badge bg-{{ $statusClass }} text-uppercase">{{ ucfirst($user->user_status) }}</span>
                        </div>
                    </div>

                    <hr>

                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Username</dt>
                        <dd class="col-7">{{ $user->user_name }}</dd>
                        <dt class="col-5 text-muted">Email</dt>
                        <dd class="col-7">{{ $user->user_email }}</dd>
                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7">{{ $user->user_telephone ?: 'Not provided' }}</dd>
                        <dt class="col-5 text-muted">Department</dt>
                        <dd class="col-7">{{ $user->department?->dept_name ?? 'N/A' }}</dd>
                        <dt class="col-5 text-muted">Gender</dt>
                        <dd class="col-7">{{ $user->user_gender ?: 'Not provided' }}</dd>
                        <dt class="col-5 text-muted">Last login</dt>
                        <dd class="col-7">{{ $user->user_last_logged_in?->format('d M Y H:i') ?? 'Never' }}</dd>
                    </dl>

                    @if($user->client)
                        <div class="mt-4 p-3 rounded" style="background:#f8fbff;">
                            <div class="fw-semibold text-dark">Client account</div>
                            <div class="small text-muted mt-2">{{ $user->client->client_name }}</div>
                            <div class="small text-muted">{{ $user->client->client_email ?? '—' }}</div>
                            <div class="small text-muted">{{ $user->client->client_contact ?? '—' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm mb-4" style="border-radius:var(--card-radius);">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3" style="color:var(--brand-blue);">Update profile</h2>
                    <p class="text-muted small mb-4">Add your face, update contact details, and keep your account information current.</p>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Surname</label>
                                <input type="text" name="user_surname" class="form-control" value="{{ old('user_surname', $user->user_surname) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Other names</label>
                                <input type="text" name="user_othername" class="form-control" value="{{ old('user_othername', $user->user_othername) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="user_email" class="form-control" value="{{ old('user_email', $user->user_email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="user_telephone" class="form-control" value="{{ old('user_telephone', $user->user_telephone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="user_gender" class="form-select">
                                    <option value="">Select</option>
                                    <option value="Male" {{ old('user_gender', $user->user_gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('user_gender', $user->user_gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('user_gender', $user->user_gender) === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile photo</label>
                                <input type="file" name="profile_photo" class="form-control" accept="image/*">
                                <div class="form-text">JPG, PNG or WEBP up to 2MB.</div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Save profile</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3" style="color:var(--brand-blue);">Change password</h2>
                    <form method="POST" action="{{ route('change-password') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Current password</label>
                            <input name="current_password" type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New password</label>
                            <input name="new_password" type="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm new password</label>
                            <input name="new_password_confirmation" type="password" class="form-control" required>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Update password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
