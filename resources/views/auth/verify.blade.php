@extends('layouts.master-login')

@section('content')
<style>
    .verify-panel { max-width: 380px; margin: 0 auto; }
    .verify-panel p { color: rgba(255,255,255,0.82); }
    .verify-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center; }
</style>

<div class="creative-bg">
    <div class="glass-tab card border-0 verify-panel">
        <div class="card-body">
            <div class="text-center mb-3">
                <div class="logo-wrapper">
                    <img src="{{ URL::asset('assets/images/centenary.png') }}" class="logo-original mb-2" alt="Centenary">
                </div>
                <h5 class="brand-title">Verify Your Email</h5>
                <div class="blue-divider"></div>
            </div>

            @if (session('resent'))
                <div class="alert alert-transparent mb-3 text-center">
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <p>Please check your email for a verification link before continuing. If you did not receive the email, click the button below to request another.</p>

            @if(Route::has('verification.resend'))
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn-creative">Resend Verification Email</button>
                </form>
            @endif

            <div class="verify-actions mt-3">
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Back to Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
