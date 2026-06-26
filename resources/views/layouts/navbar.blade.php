<!-- Main navbar -->
<div class="navbar navbar-expand-lg navbar-light border-bottom py-1" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); box-shadow: 0 2px 8px rgba(52, 150, 215, 0.1);">
    <div class="container-fluid px-3">
        <div class="d-flex align-items-center gap-2">
            <div class="navbar-brand d-flex align-items-center gap-2 py-0">
                <img src="{{ URL::asset('assets/images/centenary.png') }}" alt="Centenary" style="width:38px; height:32px;">
                <div>
                    <div style="font-size: 0.88rem; font-weight: 700; color: #3496D7; line-height: 1.15;">Support Desk</div>
                    <div style="font-size: 0.68rem; color: #3496D7; opacity: 0.75;">FLAXEM Official Portal</div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center ms-auto gap-2">
            @auth
                <a href="{{ route('profile') }}" class="d-flex align-items-center text-decoration-none text-primary" style="font-size:0.82rem;" aria-label="Open profile">
                    <img src="{{ auth()->user()->profile_photo ? asset('images/' . auth()->user()->profile_photo) : URL::asset('assets/images/centenary.png') }}"
                         alt="Profile" width="36" height="36" class="rounded-circle border" style="object-fit: cover;">
                    <div class="ms-2 text-start">
                        <div style="font-weight:700; font-size:0.82rem;">{{ auth()->user()->user_surname }} {{ auth()->user()->user_othername }}</div>
                        <div style="font-size:0.68rem; opacity:0.8;">{{ auth()->user()->department->dept_name ?? 'N/A' }}</div>
                    </div>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3" style="font-size:0.78rem;">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</div>
<!-- /main navbar -->
