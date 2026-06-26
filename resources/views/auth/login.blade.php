@extends('layouts.master-login')

@section('content')
<style>
    .creative-bg {
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

    .glass-tab {
        background: rgba(29, 105, 220, 0.12);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
        width: 100%;
        max-width: 300px;
        transition: all 0.3s ease;
    }

    .glass-tab:hover {
        background: rgba(29, 105, 220, 0.16);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.22);
    }

    .glass-tab .card-body {
        padding: 1.2rem 1rem !important;
    }

    .transparent-input {
        background: #ffffff;
        border: 1px solid rgba(29, 105, 220, 0.18);
        border-radius: 1.25rem;
        padding: 0.55rem 0.9rem;
        color: #0f172a;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .transparent-input:focus {
        background: #ffffff;
        border-color: #3496D7;
        box-shadow: 0 0 0 4px rgba(29, 105, 220, 0.12);
        color: #0f172a;
        outline: none;
    }

    .transparent-input::placeholder {
        color: rgba(15, 23, 42, 0.6);
        font-size: 0.8rem;
    }

    .form-label-light {
        color: white;
        font-weight: 500;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        margin-bottom: 0.2rem;
        display: block;
    }

    .btn-creative {
        background: linear-gradient(135deg, #3496D7, #0f4cb8);
        border: none;
        border-radius: 1.25rem;
        padding: 0.55rem;
        font-weight: 700;
        color: #ffffff;
        transition: all 0.2s ease;
        width: 100%;
        font-size: 0.85rem;
        box-shadow: 0 10px 30px rgba(29, 105, 220, 0.28);
    }

    .btn-creative:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #1f73e8, #1454c7);
        box-shadow: 0 12px 32px rgba(29, 105, 220, 0.34);
    }

    .check-light {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        font-size: 0.72rem;
    }

    .check-light input {
        width: 0.9rem;
        height: 0.9rem;
        accent-color: #3496D7;
    }

    .alert-transparent {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        border: none;
        color: white;
        border-radius: 1.25rem;
        font-size: 0.72rem;
        padding: 0.5rem 1rem;
    }

    .link-light {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-size: 0.7rem;
        transition: 0.2s;
    }

    .link-light:hover {
        color: white;
        text-decoration: underline;
    }

    /* Logo – keep original colors */
    .logo-original {
        height: 34px;
        position: relative;
        z-index: 2;
    }

    /* Blue glow behind the logo */
    .logo-wrapper {
        position: relative;
        display: inline-block;
    }

    .logo-wrapper::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 56px;
        height: 56px;
        background: radial-gradient(circle, rgba(29, 105, 220, 0.5), rgba(29, 105, 220, 0));
        border-radius: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        filter: blur(8px);
    }

    /* Blue accent text */
    .brand-title {
        color: #000205;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: -0.2px;
        text-shadow: 0 0 4px rgba(29, 105, 220, 0.3);
        margin-bottom: 0;
    }

    /* Blue divider under title */
    .blue-divider {
        width: 32px;
        height: 2px;
        background: #3496D7;
        margin: 4px auto 0;
        border-radius: 4px;
    }

    hr {
        border-color: rgba(255, 255, 255, 0.2);
        margin: 0.75rem 0;
    }

    .password-input-wrapper {
        position: relative;
    }

    .btn-toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #3496D7;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }

    .btn-toggle-password:hover {
        color: #0f4cb8;
    }

    .btn-toggle-password i {
        width: 20px;
        height: 20px;
    }

</style>

<div class="creative-bg">
    <div class="glass-tab card border-0">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-transparent mb-3 text-center">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-transparent mb-3">
                    @foreach($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mb-3">
                {{-- Logo with blue glow --}}
                <div class="logo-wrapper">
                    <img src="{{ URL::asset('assets/images/centenary.png') }}" class="logo-original mb-2" alt="Centenary">
                </div>
                {{-- Blue title + blue divider --}}
                <h5 class="brand-title">Support Desk</h5>
                <div class="blue-divider"></div>
                <p class="text-white-50 small mt-2 mb-0">secure ticketing</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label-light"><i class="ph-user me-1"></i> USERNAME</label>
                    <input type="text" name="user_name" class="transparent-input form-control @error('user_name') is-invalid @enderror"
                           value="{{ old('user_name') }}" placeholder="your.username" autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label-light"><i class="ph-lock me-1"></i> PASSWORD</label>
                    <div class="password-input-wrapper position-relative">
                        <input type="password" name="password" id="password" class="transparent-input form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••">
                        <button type="button" class="btn-toggle-password" onclick="togglePassword('password')" title="Show/Hide password">
                            <i class="ph-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="check-light">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-light">Forgot?</a>
                    @else
                        <span class="text-white-50 small">🔐 admin</span>
                    @endif
                </div>

                <button type="submit" class="btn-creative">
                    <i class="ph-arrow-right me-1"></i> Sign in
                </button>

                <hr>
                <div class="text-center mt-2">
                    <span class="text-white-50 small">⚡ Support 24/7</span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('ph-eye');
        icon.classList.add('ph-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('ph-eye-slash');
        icon.classList.add('ph-eye');
    }
}
</script>
@endsection
