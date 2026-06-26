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
                <h5 class="brand-title">Confirm Password</h5>
                <div class="blue-divider"></div>
                <p class="text-white-50 small mt-2 mb-0">Please confirm your password before continuing.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-transparent mb-3">
                    @foreach($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label-light">Password</label>
                    <input type="password" name="password"
                           class="transparent-input form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" autofocus>
                </div>

                <button type="submit" class="btn-creative">Confirm Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
