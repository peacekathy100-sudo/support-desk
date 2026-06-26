@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="h4 mb-0" style="color:var(--brand-blue);">Edit portal client</h1>
        <a href="{{ route('admin.external-clients.show', $client) }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.external-clients.update', $client) }}">
                @csrf
                @method('PUT')
                @include('admin.external-clients._form', ['client' => $client])
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
