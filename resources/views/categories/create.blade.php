@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Create Category</h1>
            <p class="text-muted mb-0">Define a new ticket category and SLA target.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">Back to categories</a>
    </div>
    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Color</label>
                        <input name="color" value="{{ old('color', '#2e7d32') }}" class="form-control" type="color" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SLA (hours)</label>
                        <input name="sla_hours" value="{{ old('sla_hours', 24) }}" class="form-control" type="number" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Active</label>
                        <select name="is_active" class="form-select">
                            <option value="1" selected>Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Save Category</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
