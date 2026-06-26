@extends('layouts.client')

@section('content')
<div class="mb-4">
    <h1 class="h4 mb-1" style="color: var(--brand-blue); font-weight: 600;">Welcome, {{ $client->full_name }}</h1>
    <p class="text-muted small mb-0">{{ $client->company_name }} • {{ now()->format('F j, Y') }}</p>
</div>

<!-- Statistics Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--brand-blue);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total Tickets</p>
                        <h3 class="mb-0" style="color: var(--brand-blue);">{{ $statistics['total'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-ticket-alt text-muted" style="font-size: 1.8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ff6b6b;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Open Tickets</p>
                        <h3 class="mb-0" style="color: #ff6b6b;">{{ $statistics['open'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-exclamation-circle text-muted" style="font-size: 1.8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffd43b;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">In Progress</p>
                        <h3 class="mb-0" style="color: #ffd43b;">{{ $statistics['in_progress'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-clock text-muted" style="font-size: 1.8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #51cf66;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Resolved</p>
                        <h3 class="mb-0" style="color: #51cf66;">{{ $statistics['resolved'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-check-circle text-muted" style="font-size: 1.8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <a href="{{ route('client.tickets.create') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm p-4 text-center" style="transition: all 0.3s ease; cursor: pointer;">
                <i class="fas fa-plus-circle" style="font-size: 2rem; color: var(--brand-blue); margin-bottom: 0.5rem;"></i>
                <h6 class="mb-1" style="color: var(--brand-blue); font-weight: 600;">Create New Ticket</h6>
                <p class="text-muted small mb-0">Submit a new support request</p>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('client.tickets.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm p-4 text-center" style="transition: all 0.3s ease; cursor: pointer;">
                <i class="fas fa-list" style="font-size: 2rem; color: var(--brand-blue); margin-bottom: 0.5rem;"></i>
                <h6 class="mb-1" style="color: var(--brand-blue); font-weight: 600;">View My Tickets</h6>
                <p class="text-muted small mb-0">Track all your support requests</p>
            </div>
        </a>
    </div>
</div>

<!-- Representative Info -->
@if($representative)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 style="color: var(--brand-blue); font-weight: 600; margin-bottom: 1rem;">Your Support Representative</h6>
        <div class="row">
            <div class="col-md-6 mb-2">
                <p class="text-muted small mb-1">Name</p>
                <p class="mb-0" style="color: #333;">{{ $representative['name'] }}</p>
            </div>
            <div class="col-md-6 mb-2">
                <p class="text-muted small mb-1">Email</p>
                <p class="mb-0" style="color: #333;"><a href="mailto:{{ $representative['email'] }}" style="color: var(--brand-blue); text-decoration: none;">{{ $representative['email'] }}</a></p>
            </div>
            @if($representative['phone'])
            <div class="col-md-6">
                <p class="text-muted small mb-1">Phone</p>
                <p class="mb-0" style="color: #333;"><a href="tel:{{ $representative['phone'] }}" style="color: var(--brand-blue); text-decoration: none;">{{ $representative['phone'] }}</a></p>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Recent Tickets Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
        <h6 style="color: var(--brand-blue); font-weight: 600; margin: 0;">Recent Tickets</h6>
    </div>
    <div class="card-body p-0">
        @forelse($recentTickets as $ticket)
        <div class="border-bottom p-3" style="transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='rgba(52,150,215,0.05)'" onmouseout="this.style.background='transparent'">
            <div class="row align-items-center">
                <div class="col-md-1 text-muted small">#{{ $ticket->ticket_number }}</div>
                <div class="col-md-4">
                    <a href="{{ route('client.tickets.show', $ticket) }}" style="color: var(--brand-blue); text-decoration: none; font-weight: 500;">
                        {{ Str::limit($ticket->subject, 40) }}
                    </a>
                </div>
                <div class="col-md-3 text-muted small">
                    {{ $ticket->created_at->format('M d, Y') }}
                </div>
                <div class="col-md-2">
                    <span class="badge" style="background: 
                        @switch($ticket->status)
                            @case('open') #ff6b6b
                            @case('in_progress') #ffd43b
                            @case('resolved') #51cf66
                            @case('closed') #868e96
                            @default var(--brand-blue)
                        @endswitch
                    ; color: white;">{{ ucfirst($ticket->status) }}</span>
                </div>
                <div class="col-md-2 text-end">
                    <a href="{{ route('client.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-secondary">View</a>
                </div>
            </div>
        </div>
        @empty
        <div class="p-4 text-center text-muted">
            <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem; display: block;"></i>
            <p class="mb-0">No tickets yet. <a href="{{ route('client.tickets.create') }}" style="color: var(--brand-blue);">Create one now</a></p>
        </div>
        @endforelse
    </div>
</div>

<style>
.card:hover {
    box-shadow: 0 8px 16px rgba(52, 150, 215, 0.1) !important;
    transform: translateY(-2px);
}
</style>
@endsection
