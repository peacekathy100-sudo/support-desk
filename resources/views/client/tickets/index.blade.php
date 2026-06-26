@extends('layouts.client')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0" style="color:var(--brand-blue);">My tickets</h1>
    <a href="{{ route('client.tickets.create') }}" class="btn btn-primary btn-sm">New ticket</a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body p-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input name="search" value="{{ $filters['search'] ?? '' }}" class="form-control form-control-sm" placeholder="Search">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    @foreach(['open','in_progress','on_hold','resolved','closed'] as $s)
                    <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-sm w-100">Filter</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>#</th><th>Subject</th><th>Priority</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->ticket_number }}</td>
                    <td>{{ $ticket->subject }}</td>
                    <td>{{ $ticket->priority }}</td>
                    <td>{{ $ticket->status }}</td>
                    <td class="text-end"><a href="{{ route('client.tickets.show', $ticket) }}">View</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No tickets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $tickets->withQueryString()->links() }}</div>
</div>
@endsection
