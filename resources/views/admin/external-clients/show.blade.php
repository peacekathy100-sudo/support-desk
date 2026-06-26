@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">{{ $client->company_name }}</h1>
            <p class="text-muted mb-0">{{ $client->full_name }} · {{ $client->email }} · {{ $client->username }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.external-clients.edit', $client) }}" class="btn btn-outline-primary btn-sm">Edit</a>
            <a href="{{ route('admin.external-clients.index') }}" class="btn btn-outline-secondary btn-sm">List</a>
            <a href="{{ route('admin.messages.index', ['client' => $client->id]) }}" class="btn btn-outline-info btn-sm">View messages</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✓ Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ ucfirst($client->status) }}</dd>
                        <dt class="col-sm-4">Representative</dt>
                        <dd class="col-sm-8">{{ $client->assignedRepresentative?->user_name ?? '—' }}</dd>
                        <dt class="col-sm-4">Phone</dt>
                        <dd class="col-sm-8">{{ $client->phone ?? '—' }}</dd>
                        <dt class="col-sm-4">Category</dt>
                        <dd class="col-sm-8">{{ $client->category ?? '—' }}</dd>
                        <dt class="col-sm-4">Notes</dt>
                        <dd class="col-sm-8">{{ $client->notes ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white"><strong>Tickets ({{ $client->tickets->count() }})</strong></div>
                <ul class="list-group list-group-flush">
                    @forelse($client->tickets->take(10) as $ticket)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $ticket->ticket_number }} — {{ $ticket->subject }}</span>
                        <span class="text-muted small">{{ $ticket->status }}</span>
                    </li>
                    @empty
                    <li class="list-group-item text-muted">No tickets.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Account actions</strong></div>
                <div class="card-body d-grid gap-2">
                    @if($client->status !== 'active')
                    <form method="POST" action="{{ route('admin.external-clients.activate', $client) }}">@csrf
                        <button class="btn btn-success btn-sm w-100">Activate</button>
                    </form>
                    @endif
                    @if($client->status !== 'suspended')
                    <form method="POST" action="{{ route('admin.external-clients.suspend', $client) }}">@csrf
                        <button class="btn btn-warning btn-sm w-100">Suspend</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.external-clients.reset-password', $client) }}">@csrf
                        <button class="btn btn-outline-secondary btn-sm w-100">Email new password</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Reassign representative</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.external-clients.reassign', $client) }}">
                        @csrf
                        <select name="assigned_to_user_id" class="form-select form-select-sm mb-2" required>
                            @foreach(\App\Models\SysUser::where('user_status', 'active')->orderBy('user_surname')->get() as $rep)
                            <option value="{{ $rep->user_id }}" @selected($client->assigned_to_user_id == $rep->user_id)>{{ $rep->user_name }} {{ $rep->user_surname }}</option>
                            @endforeach
                        </select>
                        <input name="reason" class="form-control form-control-sm mb-2" placeholder="Reason (optional)">
                        <button class="btn btn-primary btn-sm w-100">Reassign</button>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.external-clients.destroy', $client) }}" onsubmit="return confirm('Delete this portal client?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm w-100">Delete account</button>
            </form>
        </div>
    </div>
</div>
@endsection
