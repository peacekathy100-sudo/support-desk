@extends('layouts.master')

@section('content')
@php
    $user = auth()->user();
    $dashboardRole = $user->isMainAdmin()
        ? 'Main Admin'
        : ($user->isSuperUser()
            ? 'Super User'
            : ($user->isClientRep()
                ? 'Client Representative'
                : 'Support Staff'));
    $departmentName = $user->department?->dept_name ?? 'Unassigned';
@endphp

<div class="container py-4">
    <div class="card shadow-sm border-0 mb-3" style="border-radius:var(--card-radius); background: linear-gradient(135deg, rgba(52,150,215,0.08), rgba(220,53,69,0.06));">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ URL::asset('assets/images/centenary.png') }}" alt="Centenary" style="height:42px;">
                    <div>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-1">
                            <span class="badge bg-{{ $accessTone }} text-uppercase" style="font-size:0.72rem;">{{ $dashboardRole }}</span>
                            <span class="badge bg-light text-dark" style="font-size:0.72rem;">{{ $departmentName }}</span>
                        </div>
                        <h2 class="mb-1" style="font-size:1rem; color:var(--brand-blue);">Welcome back, {{ $user->user_surname }}.</h2>
                        <div class="text-muted" style="font-size:0.82rem;">
                            {{ $accessLabel }} · {{ $user->role?->ur_name ?? 'User' }} access is active.
                        </div>
                    </div>
                </div>
                <div class="text-end" style="min-width:220px;">
                    <div class="text-muted" style="font-size:0.76rem;">Current visibility</div>
                    <div class="fw-semibold" style="font-size:0.95rem; color:var(--brand-blue);">{{ $user->isMainAdmin() ? 'All departments' : ($user->isSuperUser() ? 'Department-only' : 'Personal scope') }}</div>
                    <div class="text-muted" style="font-size:0.8rem;">{{ $user->isMainAdmin() ? 'System-wide insights and controls.' : ($user->isSuperUser() ? 'Focused on your department and its workflows.' : 'Your tickets, leave, and assigned work only.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted" style="font-size:0.78rem;">Tickets</div>
                    <div class="h3 mb-0" style="font-size:1.1rem;">{{ $ticketStats['total'] ?? 0 }}</div>
                    <div class="text-success" style="font-size:0.78rem;">Open: {{ $ticketStats['open'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        @if($isAdminOrSuperUser)
        <div class="col-md-3">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted" style="font-size:0.78rem;">Clients</div>
                    <div class="h3 mb-0" style="font-size:1.1rem;">{{ $clientStats['total'] ?? 0 }}</div>
                    <div class="text-primary" style="font-size:0.78rem;">Active: {{ $clientStats['active'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted" style="font-size:0.78rem;">Department users</div>
                    <div class="h3 mb-0" style="font-size:1.1rem;">{{ $departmentUsers ?? 0 }}</div>
                    <div class="text-muted" style="font-size:0.78rem;">Active staff in {{ $departmentName }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted" style="font-size:0.78rem;">System users</div>
                    <div class="h3 mb-0" style="font-size:1.1rem;">{{ $userStats['total'] ?? 0 }}</div>
                    <div class="text-success" style="font-size:0.78rem;">Online: {{ $userStats['online'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        @else
        @if($myTicketStats)
        <div class="col-md-3">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted" style="font-size:0.78rem;">Your Resolved</div>
                    <div class="h3 mb-0" style="font-size:1.1rem;">{{ $myTicketStats['solved'] ?? 0 }}</div>
                    <div class="text-info" style="font-size:0.78rem;">Tickets you've solved</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted" style="font-size:0.78rem;">Assigned to You</div>
                    <div class="h3 mb-0" style="font-size:1.1rem;">{{ $myTicketStats['assigned'] ?? 0 }}</div>
                    <div class="text-warning" style="font-size:0.78rem;">Pending resolution</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="text-muted" style="font-size:0.78rem;">Your Closed</div>
                    <div class="h3 mb-0" style="font-size:1.1rem;">{{ $myTicketStats['closed'] ?? 0 }}</div>
                    <div class="text-success" style="font-size:0.78rem;">Tickets you closed</div>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>

    <div class="row g-3">
        @if($isAdminOrSuperUser)
        <div class="col-lg-7">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-2" style="font-size:0.95rem; color:var(--brand-blue);">Recent System Updates</h5>
                    @if(isset($recentAudits) && $recentAudits->count())
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0" style="font-size:0.85rem;">
                                <thead class="text-muted" style="font-size:0.78rem;">
                                    <tr>
                                        <th>Action</th>
                                        <th>Section</th>
                                        <th>User</th>
                                        <th class="text-end">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($recentAudits as $audit)
                                    <tr>
                                        <td class="text-capitalize">{{ str_replace('_', ' ', $audit->action ?? '—') }}</td>
                                        <td>{{ $audit->model ?? 'System' }}</td>
                                        <td>{{ $audit->user?->full_name ?: 'System' }}</td>
                                        <td class="text-end text-muted">{{ $audit->created_at?->diffForHumans() ?? '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted" style="font-size:0.9rem;">No recent system updates to show.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-3" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-2" style="font-size:0.95rem; color:var(--brand-blue);">Tickets by Status</h5>
                    <div class="mt-2" style="font-size:0.85rem;">
                        @foreach($ticketsByStatus as $k => $v)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="text-capitalize text-muted" style="font-size:0.82rem;">{{ str_replace('_', ' ', $k) }}</div>
                                <div class="fw-semibold">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-2" style="font-size:0.95rem; color:var(--brand-blue);">This Month / Last Month</h5>
                    <div class="d-flex justify-content-between align-items-center" style="font-size:0.95rem;">
                        <div class="text-muted">This month</div>
                        <div class="fw-semibold">{{ $thisMonth ?? 0 }}</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1" style="font-size:0.95rem;">
                        <div class="text-muted">Last month</div>
                        <div class="fw-semibold">{{ $lastMonth ?? 0 }}</div>
                    </div>
                </div>
            </div>

            @if($recentLogins->count())
            <div class="card shadow-sm mt-3" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-2" style="font-size:0.95rem; color:var(--brand-blue);">Recent Logins</h5>
                    <div class="list-group list-group-flush">
                        @foreach($recentLogins as $login)
                            <div class="list-group-item px-0 py-2" style="font-size:0.85rem;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ $login->user_surname }}</div>
                                        <div class="text-muted" style="font-size:0.78rem;">{{ $login->role?->ur_name ?? 'User' }} · {{ $login->department?->dept_name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="text-muted text-end" style="font-size:0.78rem;">{{ $login->user_last_logged_in?->diffForHumans() ?? '—' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-3" style="font-size:0.95rem; color:var(--brand-blue);">Your Ticket Summary</h5>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <div class="text-muted" style="font-size:0.85rem;">Open Tickets</div>
                                <div class="h4 mb-0" style="color:var(--brand-blue);">{{ $ticketStats['open'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <div class="text-muted" style="font-size:0.85rem;">Resolved This Month</div>
                                <div class="h4 mb-0" style="color:var(--brand-blue);">{{ $thisMonth ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <div class="text-muted" style="font-size:0.85rem;">Overdue Tickets</div>
                                <div class="h4 mb-0" style="color:#dc3545;">{{ $ticketStats['overdue'] ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <div class="text-muted" style="font-size:0.85rem;">Leave Pending</div>
                                <div class="h4 mb-0" style="color:var(--brand-blue);">{{ $leaveStats['pending'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-3" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-2" style="font-size:0.95rem; color:var(--brand-blue);">Your Recent Tickets</h5>
                    @if($recentTickets->count())
                        <div class="list-group list-group-flush">
                            @foreach($recentTickets as $ticket)
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="list-group-item list-group-item-action px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div style="flex:1;">
                                            <div class="fw-semibold" style="font-size:0.9rem;">{{ $ticket->title ?? 'Untitled' }}</div>
                                            <div class="text-muted" style="font-size:0.78rem;">{{ $ticket->category?->cat_name ?? 'General' }} · <span class="badge bg-secondary" style="font-size:0.7rem;">{{ $ticket->status ?? 'open' }}</span></div>
                                        </div>
                                        <div class="text-muted text-end" style="font-size:0.78rem;">{{ $ticket->created_at?->diffForHumans() ?? '—' }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted text-center py-3">No recent tickets to display.</div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
