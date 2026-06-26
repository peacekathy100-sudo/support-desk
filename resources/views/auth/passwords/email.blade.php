@extends('layouts.master-login')

@section('content')
<style>
    .auth-panel { max-width: 380px; margin: 0 auto; }
</style>

<div class="creative-bg">
    <div class="glass-tab card border-0 auth-panel">
        <div class="card-body">
            <div class="text-center mb-3">
                <div class="logo-wrapper">
                    <img src="{{ URL::asset('assets/images/centenary.png') }}" class="logo-original mb-2" alt="Centenary">
                </div>
                <h5 class="brand-title">Reset Password</h5>
                <div class="blue-divider"></div>
                <p class="text-white-50 small mt-2 mb-0">Enter your email to receive reset instructions.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-transparent mb-3 text-center">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-transparent mb-3">
                    @foreach($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label-light">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="transparent-input form-control @error('email') is-invalid @enderror"
                           placeholder="email@example.com" autofocus>
                </div>

                <button type="submit" class="btn-creative">Send Reset Link</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="link-light">Back to sign in</a>
            </div>
        </div>
    </div>
</div>
@endsection
