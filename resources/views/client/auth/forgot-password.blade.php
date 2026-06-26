@extends('layouts.master-login')

@section('content')
<style>
    .creative-bg-client {
        background: linear-gradient(120deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)),
                    url('https://www.capterra.com/assets-bx-capterra/_next/image?url=https%3A%2F%2Fimages.ctfassets.net%2Fpx6a31ta05xu%2F62OwHjP2e1E2k6etJ9qy6T%2F2e38ca627e42b903126a6443a72d77b7%2FAdobeStock_1484695229.jpg&w=1920&q=75');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
</style>

<div class="creative-bg-client">
    <div class="card shadow-lg" style="max-width:420px; width:100%; border-radius:1rem;">
        <div class="card-body p-4">
            <h1 class="h5 mb-3 text-center" style="color:#3496D7;">Reset password</h1>

            @if(session('status'))
                <div class="alert alert-info small">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('client.forgot-password.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send reset link</button>
            </form>
            <p class="text-center small mt-3 mb-0">
                <a href="{{ route('client.login') }}">Back to login</a>
            </p>
        </div>
    </div>
</div>
@endsection
