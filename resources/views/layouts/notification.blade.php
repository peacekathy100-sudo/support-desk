@auth
@php
    $authUser      = auth()->user();
    $allNotifs     = $authUser->ticketNotifications()->with('ticket')->take(25)->get();
    $unreadNotifs  = $allNotifs->where('is_read', false);
    $readNotifs    = $allNotifs->where('is_read', true);
@endphp

<!-- Notifications offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="notifications" style="max-width:380px;">

    {{-- Header --}}
    <div class="offcanvas-header py-0 border-bottom">
        <h5 class="offcanvas-title py-3 fw-bold d-flex align-items-center gap-2">
            <i class="ph-bell text-primary"></i>
            Notifications
            @if($unreadNotifs->count() > 0)
                <span class="badge bg-danger rounded-pill" style="font-size:0.7rem;">
                    {{ $unreadNotifs->count() }}
                </span>
            @endif
        </h5>
        <div class="d-flex align-items-center gap-2">
            @if($unreadNotifs->count() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST" class="no-loader">
                @csrf
                <button type="submit"
                        class="btn btn-sm btn-outline-secondary rounded-pill"
                        style="font-size:0.75rem;">
                    Mark all read
                </button>
            </form>
            @endif
            <button type="button"
                    class="btn btn-light btn-sm btn-icon border-transparent rounded-pill"
                    data-bs-dismiss="offcanvas">
                <i class="ph-x"></i>
            </button>
        </div>
    </div>

    {{-- Body --}}
    <div class="offcanvas-body p-0" style="overflow-y:auto;">

        @if($allNotifs->isEmpty())
        <div class="text-center text-muted py-5 px-3">
            <i class="ph-bell-slash" style="font-size:3rem; opacity:.4;"></i>
            <p class="mt-3 mb-0 fw-semibold">You're all caught up!</p>
            <small>No notifications yet.</small>
        </div>

        @else

            {{-- ── UNREAD ─────────────────────────────────────── --}}
            @if($unreadNotifs->count() > 0)
            <div class="bg-primary bg-opacity-10 fw-semibold py-2 px-3 small text-primary border-bottom">
                New &amp; unread
            </div>
            @foreach($unreadNotifs as $notif)
                @include('layouts.partials.notif-item', ['notif' => $notif, 'unread' => true])
            @endforeach
            @endif

            {{-- ── READ / OLDER ───────────────────────────────── --}}
            @if($readNotifs->count() > 0)
            <div class="bg-light fw-semibold py-2 px-3 small text-muted border-top border-bottom">
                Older notifications
            </div>
            @foreach($readNotifs as $notif)
                @include('layouts.partials.notif-item', ['notif' => $notif, 'unread' => false])
            @endforeach
            @endif

        @endif

    </div>
</div>
<!-- /notifications -->
@endauth
