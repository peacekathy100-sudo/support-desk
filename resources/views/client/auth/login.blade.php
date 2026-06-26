@extends('layouts.master-login')

@section('content')
<style>
    .creative-bg-client {
        background: linear-gradient(120deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)),
                    url('{{ asset('images/client-login-bg.jpg') }}');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .glass-tab-client {
        background: rgba(29, 105, 220, 0.12);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
        width: 100%;
        max-width: 350px;
        transition: all 0.3s ease;
    }

    .glass-tab-client:hover {
        background: rgba(29, 105, 220, 0.16);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.22);
    }

    .glass-tab-client .card-body {
        padding: 1.5rem 1.2rem !important;
    }

    .transparent-input-client {
        background: #ffffff;
        border: 1px solid rgba(29, 105, 220, 0.18);
        border-radius: 1.25rem;
        padding: 0.65rem 1rem;
        color: #0f172a;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .transparent-input-client:focus {
        background: #ffffff;
        border-color: #3496D7;
        box-shadow: 0 0 0 4px rgba(29, 105, 220, 0.12);
        color: #0f172a;
        outline: none;
    }

    .transparent-input-client::placeholder {
        color: rgba(15, 23, 42, 0.6);
        font-size: 0.85rem;
    }

    .form-label-light-client {
        color: white;
        font-weight: 500;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        margin-bottom: 0.4rem;
        display: block;
    }

    .btn-creative-client {
        background: linear-gradient(135deg, #3496D7, #0f4cb8);
        border: none;
        border-radius: 1.25rem;
        padding: 0.65rem;
        font-weight: 700;
        color: #ffffff;
        transition: all 0.2s ease;
        width: 100%;
        font-size: 0.9rem;
        box-shadow: 0 10px 30px rgba(29, 105, 220, 0.28);
    }

    .btn-creative-client:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #1f73e8, #1454c7);
        box-shadow: 0 12px 32px rgba(29, 105, 220, 0.34);
        color: white;
    }

    .check-light-client {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        font-size: 0.8rem;
    }

    .check-light-client input {
        width: 1rem;
        height: 1rem;
        accent-color: #3496D7;
    }

    .alert-transparent-client {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        border: none;
        color: white;
        border-radius: 1.25rem;
        font-size: 0.8rem;
        padding: 0.6rem 1rem;
    }

    .link-light-client {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-size: 0.8rem;
        transition: 0.2s;
    }

    .link-light-client:hover {
        color: white;
        text-decoration: underline;
    }

    /* Logo */
    .logo-client-wrapper {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .logo-original-client {
        height: 60px;
        margin-bottom: 0.8rem;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
    }

    /* Blue title + blue divider */
    .brand-title-client {
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: -0.2px;
        text-shadow: 0 0 4px rgba(29, 105, 220, 0.3);
        margin-bottom: 0.4rem;
    }

    .blue-divider-client {
        width: 40px;
        height: 2px;
        background: #3496D7;
        margin: 0 auto;
        border-radius: 4px;
    }

    .subtitle-client {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.75rem;
        letter-spacing: 1px;
        margin-top: 0.5rem;
        text-transform: uppercase;
    }

    hr {
        border-color: rgba(255, 255, 255, 0.15);
        margin: 0.8rem 0;
    }

    .password-input-wrapper {
        position: relative;
    }

    .btn-toggle-password {
        position: absolute;
        right: 14px;
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

    @media (max-width: 767px) {
        .glass-tab-client { max-width: 100%; }
        .glass-tab-client .card-body { padding: 1.2rem !important; }
    }
</style>

<div class="creative-bg-client">
    <div class="glass-tab-client card border-0">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-transparent-client mb-3 text-center">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-transparent-client mb-3">
                    @foreach($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="logo-client-wrapper">
                <img src="{{ asset('images/fse-logo.png') }}" class="logo-original-client" alt="FSE Logo">
                <h5 class="brand-title-client">Client Portal</h5>
                <div class="blue-divider-client"></div>
                <p class="subtitle-client">secure access</p>
            </div>

            <form method="POST" action="{{ route('client.login.post') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label-light-client"><i class="ph-user me-1"></i> USERNAME</label>
                    <input type="text" name="username" class="transparent-input-client form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" placeholder="your.username" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label-light-client"><i class="ph-lock me-1"></i> PASSWORD</label>
                    <div class="password-input-wrapper position-relative">
                        <input type="password" name="password" id="client-password" class="transparent-input-client form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••" required>
                        <button type="button" class="btn-toggle-password" onclick="togglePassword('client-password')" title="Show/Hide password">
                            <i class="ph-eye" id="client-password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="check-light-client">
                        <input type="checkbox" name="remember" value="1"> Remember me
                    </label>
                    <a href="{{ route('client.forgot-password') }}" class="link-light-client">Forgot?</a>
                </div>

                <button type="submit" class="btn-creative-client">
                    <i class="ph-arrow-right me-1"></i> Sign in
                </button>

                <hr>
                <div class="text-center mt-2">
                    <span style="color: rgba(255, 255, 255, 0.7); font-size: 0.75rem;">🔐 24/7 Support Portal</span>
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
