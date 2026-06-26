@extends('layouts.master-login')

@section('content')
<style>
    .form-group { margin-bottom: 1rem; }
    .btn-creative { width: 100%; }
    .auth-footer { font-size: 0.8rem; color: rgba(255,255,255,0.72); }
</style>

<div class="creative-bg">
    <div class="glass-tab card border-0">
        <div class="card-body">
            <div class="text-center mb-3">
                <div class="logo-wrapper">
                    <img src="{{ URL::asset('assets/images/centenary.png') }}" class="logo-original mb-2" alt="Centenary">
                </div>
                <h5 class="brand-title">Create Your Account</h5>
                <div class="blue-divider"></div>
                <p class="text-white-50 small mt-2 mb-0">Register and manage tickets securely.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-transparent mb-3">
                    @foreach($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label-light">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="transparent-input form-control @error('name') is-invalid @enderror"
                           placeholder="John Doe">
                </div>

                <div class="form-group">
                    <label class="form-label-light">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="transparent-input form-control @error('email') is-invalid @enderror"
                           placeholder="email@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label-light">Avatar</label>
                    <input type="file" name="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label-light">Password</label>
                    <input type="password" name="password"
                           class="transparent-input form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="form-label-light">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="transparent-input form-control"
                           placeholder="••••••••">
                </div>

                <button type="submit" class="btn-creative">Create Account</button>
            </form>

            <div class="text-center mt-3 auth-footer">
                Already registered? <a href="{{ route('login') }}" class="link-light">Sign in</a>
            </div>
        </div>
    </div>
</div>
@endsection
