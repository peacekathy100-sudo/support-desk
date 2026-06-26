@extends('layouts.client')

@section('content')
<h1 class="h4 mb-3" style="color:var(--brand-blue);">Profile</h1>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('client.profile.update') }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input name="full_name" class="form-control" value="{{ old('full_name', $client->full_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email', $client->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input class="form-control" value="{{ $client->username }}" disabled>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
