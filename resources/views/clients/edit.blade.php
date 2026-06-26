@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Edit Client</h1>
            <p class="text-muted mb-0">Update this client profile and keep details current.</p>
        </div>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to list
        </a>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show py-3" role="alert">
                    <h6 class="alert-heading mb-2">
                        <i class="bi bi-exclamation-circle"></i> Validation Error
                    </h6>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('clients.update', $client) }}" novalidate>
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <!-- Client Code (Read-only) -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Client Code</label>
                        <input 
                            value="{{ $client->client_code }}" 
                            class="form-control-plaintext fw-bold text-primary" 
                            readonly>
                        <small class="text-muted">Auto-generated</small>
                    </div>

                    <!-- Client Name -->
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                        <input 
                            name="client_name" 
                            value="{{ old('client_name', $client->client_name) }}" 
                            class="form-control @error('client_name') is-invalid @enderror" 
                            required 
                            maxlength="150">
                        @error('client_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input 
                            name="client_email" 
                            type="email" 
                            value="{{ old('client_email', $client->client_email) }}" 
                            class="form-control @error('client_email') is-invalid @enderror"
                            maxlength="150">
                        @error('client_email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Representative -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Representative Name</label>
                        <input 
                            name="client_representative" 
                            value="{{ old('client_representative', $client->client_representative) }}" 
                            class="form-control @error('client_representative') is-invalid @enderror"
                            maxlength="150">
                        @error('client_representative')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea 
                            name="client_address" 
                            rows="3" 
                            class="form-control @error('client_address') is-invalid @enderror">{{ old('client_address', $client->client_address) }}</textarea>
                        @error('client_address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contact Number -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input 
                            name="client_contact" 
                            value="{{ old('client_contact', $client->client_contact) }}" 
                            class="form-control @error('client_contact') is-invalid @enderror"
                            maxlength="20">
                        @error('client_contact')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check w-100">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                value="1" 
                                {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <span class="badge bg-success">Active</span> Client is available
                            </label>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="col-12 border-top pt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">
                                    <strong>Created:</strong> {{ $client->created_at->format('Y-m-d H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">
                                    <strong>Updated:</strong> {{ $client->updated_at->format('Y-m-d H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-12 text-end border-top pt-3">
                        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">
                            <i class="bi bi-check-circle"></i> Update Client
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    .text-danger {
        color: #dc3545;
        font-weight: bold;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
    }
    .form-control-plaintext {
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
        padding-left: 0;
    }
</style>
@endsection
