@php
    /* ── Icon & colour per notification type ─────────────────── */
    $meta = match($notif->type) {
        'new_ticket'       => ['ph-ticket',              'bg-warning  bg-opacity-15 text-warning'],
        'ticket_created'   => ['ph-check-circle',        'bg-success  bg-opacity-15 text-success'],
        'ticket_assigned'  => ['ph-user-circle-plus',    'bg-primary  bg-opacity-15 text-primary'],
        'status_changed'   => ['ph-arrows-clockwise',    'bg-purple   bg-opacity-15 text-purple'],
        'comment_added'    => ['ph-chat-circle-text',    'bg-info     bg-opacity-15 text-info'],
        'ticket_reopened'  => ['ph-arrow-u-up-left',     'bg-danger   bg-opacity-15 text-danger'],
        default            => ['ph-bell',                'bg-secondary bg-opacity-15 text-secondary'],
    };
    [$icon, $iconClass] = $meta;

    $ticketUrl = $notif->ticket ? route('tickets.show', $notif->ticket) : '#';
@endphp

<div class="d-flex align-items-start gap-3 px-3 py-3 border-bottom notif-row {{ $unread ? 'bg-white' : 'bg-light bg-opacity-50' }}"
     style="cursor:pointer; transition:background .15s;"
     onclick="markNotifRead({{ $notif->id }}, '{{ $ticketUrl }}')"
     onmouseenter="this.style.background='#f0f4ff'" onmouseleave="this.style.background=''">

    {{-- Icon bubble --}}
    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center {{ $iconClass }}"
         style="width:38px; height:38px; min-width:38px;">
        <i class="{{ $icon }}" style="font-size:1.1rem;"></i>
    </div>

    {{-- Content --}}
    <div class="flex-fill" style="min-width:0;">
        <div class="small text-dark lh-sm" style="word-break:break-word;">
            {{ $notif->message }}
        </div>
        @if($notif->ticket)
        <div class="text-primary small mt-1 fw-semibold" style="font-size:0.72rem;">
            {{ $notif->ticket->ticket_number }}
        </div>
        @endif
        <div class="text-muted mt-1" style="font-size:0.7rem;">
            {{ $notif->created_at->diffForHumans() }}
        </div>
    </div>

    {{-- Unread dot --}}
    @if($unread)
    <div class="flex-shrink-0 mt-1">
        <span class="bg-primary rounded-circle d-inline-block"
              style="width:8px; height:8px; min-width:8px;"></span>
    </div>
    @endif
</div>

@once
@push('scripts')
<script>
function markNotifRead(id, url) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).finally(() => {
        /* Update badge count */
        const badge = document.getElementById('nav-notif-badge');
        if (badge) {
            const current = parseInt(badge.textContent) || 0;
            if (current <= 1) badge.remove();
            else badge.textContent = current - 1;
        }
        window.location.href = url;
    });
}
</script>
@endpush
@endonce
