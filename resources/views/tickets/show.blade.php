@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Ticket Details</h1>
            <p class="text-muted mb-0">Review ticket history, update status, and collaborate on this ticket.</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm">Back to tickets</a>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3 gap-3">
                        <div>
                            <h2 class="h5 mb-1">{{ $ticket->subject }}</h2>
                            <div class="text-muted" style="font-size:0.9rem;">{{ $ticket->ticket_number }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge" style="background:{{ $ticket->status_color }}; color:#fff;">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                            <div class="mt-1"><span class="badge" style="background:{{ $ticket->priority_color }}; color:#fff;">{{ ucfirst($ticket->priority) }}</span></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Client</div>
                            <div>{{ $ticket->client?->client_name ?? 'Internal' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Category</div>
                            <div>{{ $ticket->category?->name ?? 'General' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Assigned to</div>
                            <div>{{ $ticket->assignee?->full_name ?? 'Unassigned' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted" style="font-size:0.82rem;">Due date</div>
                            <div>{{ $ticket->due_at?->format('M j, Y') ?? 'Not set' }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold mb-1">Description</div>
                        <div class="text-muted">{{ $ticket->description }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold mb-2">Ticket activity</div>
                        @if($ticket->history->count())
                            <ul class="list-unstyled mb-0">
                                @foreach($ticket->history as $event)
                                    <li class="mb-2 p-2 rounded" style="background:#f8f9fa;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="text-muted" style="font-size:0.85rem;">{{ $event->changed_at?->format('M j, Y H:i') ?? '' }}</div>
                                            <div class="badge bg-secondary">{{ $event->changed_by ? 'User' : 'System' }}</div>
                                        </div>
                                        <div>{{ $event->description ?? 'Update recorded.' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted">No ticket history available.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-3" style="color:var(--brand-blue);">Comments</h5>
                    @if($ticket->comments->count())
                        @foreach($ticket->comments as $comment)
                            <div class="mb-3 p-3 rounded" style="background:#f8f9fa;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="fw-semibold">{{ $comment->author?->full_name ?? 'Staff' }}</div>
                                    <div class="text-muted small">{{ $comment->created_at?->format('M j, Y H:i') }}</div>
                                </div>
                                <div>{{ $comment->comment }}</div>
                                @if($comment->is_internal)
                                    <div class="badge bg-secondary mt-2">Internal note</div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted">No comments yet.</div>
                    @endif

                    <form method="POST" action="{{ route('tickets.comment', $ticket) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">New comment</label>
                            <textarea name="comment" rows="4" class="form-control" required>{{ old('comment') }}</textarea>
                        </div>
                        @if(auth()->user()->isAgent())
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_internal" name="is_internal" value="1" {{ old('is_internal') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_internal">Internal note</label>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary btn-sm">Add comment</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow-sm mb-3" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-3" style="color:var(--brand-blue);">Update Status</h5>
                    <form method="POST" action="{{ route('tickets.status', $ticket) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['open'=>'Open','in_progress'=>'In Progress','on_hold'=>'On Hold','resolved'=>'Resolved','closed'=>'Closed'] as $key => $label)
                                    <option value="{{ $key }}" {{ $ticket->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Resolution note</label>
                            <textarea name="resolution_note" rows="3" class="form-control">{{ old('resolution_note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Save status</button>
                    </form>
                </div>
            </div>
            @if(auth()->user()->isMainAdmin())
                <div class="card shadow-sm mb-3" style="border-radius:var(--card-radius);">
                    <div class="card-body p-3">
                        <h5 class="mb-3" style="color:var(--brand-blue);">Assign Agents</h5>
                        <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
                            @csrf
                            <div class="mb-3">
                                <select name="agent_ids[]" class="form-select" multiple size="5">
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->user_id }}" {{ $ticket->assignees->pluck('user_id')->contains($agent->user_id) ? 'selected' : '' }}>{{ $agent->full_name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Assign</button>
                        </form>
                    </div>
                </div>
            @endif
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-3" style="color:var(--brand-blue);">Attachments</h5>
                    @if($ticket->attachments->count())
                        <ul class="list-group list-group-flush">
                            @foreach($ticket->attachments as $attachment)
                                <li class="list-group-item py-2">
                                    <a href="{{ route('attachments.view', $attachment) }}" class="text-decoration-none">{{ $attachment->file_name }}</a>
                                    <div class="text-muted small">{{ $attachment->file_size_formatted ?? '–' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No attachments uploaded.</div>
                    @endif
                    <form method="POST" action="{{ route('tickets.attachments', $ticket) }}" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="attachments[]" class="form-control form-control-sm" multiple>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-sm w-100">Upload files</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
