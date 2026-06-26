<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Client Portal — {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ URL::asset('assets/images/centenary-small.png') }}">
    @include('layouts.head-css')
    <style>
        :root { 
            --brand-blue: #3496D7;
            --brand-dark-blue: #2980BB;
            --brand-light-blue: #e8f3ff;
            --brand-accent: #FF4444;
            --card-radius: 10px; 
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; flex-direction: column; overflow: hidden; background: #ffffff; }

        /* Top Red Accent Bar */
        .client-top-bar {
            height: 5px;
            background: linear-gradient(90deg, #FF4444 0%, #FF6B6B 50%, #FF4444 100%);
            box-shadow: 0 2px 8px rgba(255, 68, 68, 0.3);
            flex-shrink: 0;
        }

        /* Navbar */
        .client-navbar {
            background: linear-gradient(90deg, var(--brand-blue) 0%, var(--brand-dark-blue) 100%);
            color: white;
            padding: 0.8rem 1.5rem;
            box-shadow: 0 4px 16px rgba(52, 150, 215, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            gap: 2rem;
        }

        .client-navbar-brand { 
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .client-navbar-logo {
            height: 50px;
            width: auto;
            max-width: 60px;
        }

        .client-navbar-brand small { display: none; }

        /* Main Container */
        .client-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Sidebar */
        .client-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #f5f9ff 0%, #e8f3ff 100%);
            border-right: 2px solid var(--brand-blue);
            overflow-y: auto;
            flex-shrink: 0;
            padding: 1.5rem 0;
            box-shadow: 4px 0 12px rgba(52, 150, 215, 0.15);
        }

        /* User Info in Sidebar */
        .sidebar-user-info {
            padding: 1.2rem 1.5rem;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-dark-blue));
            color: white;
            margin: 0 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(52, 150, 215, 0.25);
            border-left: 4px solid #FF4444;
        }

        .sidebar-user-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-user-company {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.8rem;
            border: 2px solid rgba(255, 255, 255, 0.5);
            font-size: 1.2rem;
        }

        /* Menu Sections */
        .sidebar-section-title {
            padding: 0.8rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--brand-blue);
            letter-spacing: 0.5px;
            border-left: 3px solid var(--brand-blue);
            margin-left: 1.5rem;
            padding-left: 1.2rem;
        }

        .sidebar-menu { list-style: none; margin-bottom: 1rem; }
        .sidebar-menu li { margin: 0; }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 1.5rem;
            color: #495057;
            text-decoration: none;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
        }

        .sidebar-menu a:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(52, 150, 215, 0.08), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .sidebar-menu a:hover {
            color: var(--brand-blue);
            border-left-color: var(--brand-blue);
            background: rgba(52, 150, 215, 0.08);
        }

        .sidebar-menu a:hover:before {
            opacity: 1;
        }

        .sidebar-menu a.active {
            background: linear-gradient(90deg, rgba(52, 150, 215, 0.15), transparent);
            color: var(--brand-blue);
            border-left-color: var(--brand-blue);
            font-weight: 600;
        }

        .sidebar-menu a.active:before {
            opacity: 1;
        }

        .sidebar-menu a i, .sidebar-menu a em { 
            font-size: 1.1rem; 
            width: 20px; 
            text-align: center;
            color: var(--brand-blue);
        }

        .sidebar-menu a:hover i,
        .sidebar-menu a.active i {
            transform: scale(1.2);
        }

        /* Sidebar Divider */
        .sidebar-divider {
            margin: 1rem 0;
            border-top: 1px solid rgba(52, 150, 215, 0.2);
        }

        /* Content Area */
        .client-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }

        /* Scrollbar */
        .client-sidebar::-webkit-scrollbar,
        .content-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .client-sidebar::-webkit-scrollbar-track,
        .content-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        .client-sidebar::-webkit-scrollbar-thumb,
        .content-wrapper::-webkit-scrollbar-thumb {
            background: rgba(52, 150, 215, 0.3);
            border-radius: 4px;
        }

        .client-sidebar::-webkit-scrollbar-thumb:hover,
        .content-wrapper::-webkit-scrollbar-thumb:hover {
            background: rgba(52, 150, 215, 0.5);
        }

        /* Alerts */
        .alert { border-radius: var(--card-radius); border: 0; border-left: 4px solid; }
        .alert-success { background: #d4edda; color: #155724; border-left-color: var(--brand-blue); }
        .alert-danger { background: #f8d7da; color: #721c24; border-left-color: #FF4444; }
        .alert-info { background: #d1ecf1; color: #0c5460; border-left-color: var(--brand-blue); }

        .btn-light {
            background: white;
            color: var(--brand-blue);
            border: 2px solid var(--brand-blue);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background: var(--brand-blue);
            color: white;
            box-shadow: 0 4px 12px rgba(52, 150, 215, 0.3);
        }

        @media (max-width: 768px) {
            .client-container { flex-direction: column; }
            .client-sidebar { width: 100%; border-right: none; border-bottom: 2px solid var(--brand-blue); max-height: 200px; }
            .sidebar-menu a { padding: 0.6rem 1.2rem; font-size: 0.9rem; }
            .sidebar-user-info { margin: 0 0.5rem 1rem; }
        }
    </style>
</head>
<body class="bg-light">
    @auth('client')
    <!-- Top Red Accent Bar -->
    <div class="client-top-bar"></div>

    <!-- Navbar -->
    <div class="client-navbar">
        <div class="client-navbar-brand">
            <img src="{{ asset('images/fse-logo.png') }}" alt="FSE Logo" class="client-navbar-logo">
        </div>
        <div>
            <form action="{{ route('client.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light btn-sm">Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Container -->
    <div class="client-container">
        <!-- Sidebar -->
        <aside class="client-sidebar">
            <!-- User Info -->
            <div class="sidebar-user-info">
                <div class="sidebar-user-avatar">
                    {{ substr(auth('client')->user()->full_name, 0, 1) }}
                </div>
                <div class="sidebar-user-name">{{ auth('client')->user()->full_name }}</div>
                <div class="sidebar-user-company">{{ auth('client')->user()->company_name }}</div>
            </div>

            <!-- Main Navigation -->
            <div class="sidebar-section-title">Navigation</div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('client.dashboard') }}" class="@if(Route::currentRouteName() == 'client.dashboard') active @endif"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="{{ route('client.tickets.index') }}" class="@if(str_contains(Route::currentRouteName(), 'tickets')) active @endif"><i class="fas fa-ticket-alt"></i> My Tickets</a></li>
                <li><a href="{{ route('client.messages.index') }}" class="@if(str_contains(Route::currentRouteName(), 'messages')) active @endif"><i class="fas fa-envelope"></i> Messages</a></li>
            </ul>

            <!-- Resources Section -->
            <div class="sidebar-section-title">Resources</div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('client.knowledge.index') }}" class="@if(str_contains(Route::currentRouteName(), 'knowledge')) active @endif"><i class="fas fa-book"></i> Knowledge Base</a></li>
                <li><a href="{{ route('client.announcements.index') }}" class="@if(str_contains(Route::currentRouteName(), 'announcements')) active @endif"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            </ul>

            <!-- Account Section -->
            <div class="sidebar-section-title">Account</div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('client.profile') }}" class="@if(Route::currentRouteName() == 'client.profile') active @endif"><i class="fas fa-user-circle"></i> My Profile</a></li>
                <li><a href="{{ route('client.profile.change-password') }}" class="@if(Route::currentRouteName() == 'client.profile.change-password') active @endif"><i class="fas fa-lock"></i> Change Password</a></li>
            </ul>
        </aside>

        <!-- Content -->
        <div class="client-content">
            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif
                @if(session('status'))
                    <div class="alert alert-info mb-3">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
        <!-- Red Footer Line -->
        <div style="
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--brand-blue) 0%, #FF4444 50%, var(--brand-blue) 100%);
            box-shadow: 0 -3px 12px rgba(52, 150, 215, 0.3);
            z-index: 1000;
        "></div>
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@latest/dist/echo.iife.js"></script>
    <script src="https://js.pusher.com/8.0.0/pusher.min.js"></script>
    <script src="{{ asset('js/chat.js') }}"></script>
    @stack('scripts')
</body>
</html>
