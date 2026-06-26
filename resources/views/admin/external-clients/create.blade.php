@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="h4 mb-0" style="color:var(--brand-blue);">New portal client</h1>
        <a href="{{ route('admin.external-clients.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <strong>✓ Auto-Generated Password:</strong> A secure password will be automatically generated and sent to the client's email address after account creation. No need to enter it manually.
            </div>
            
            <form method="POST" action="{{ route('admin.external-clients.store') }}" novalidate>
                @csrf
                @include('admin.external-clients._form', ['client' => null])
                
                <div class="text-end mt-4">
                    <a href="{{ route('admin.external-clients.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.is-invalid {
    border-color: #dc3545;
}
.invalid-feedback {
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}
</style>
@endsection
