<!-- Main sidebar -->
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg" style="background: linear-gradient(180deg, #3496D7 0%, #3496D7 100%);">
    <div class="sidebar-content" style="min-height:100vh; display:flex; flex-direction:column; overflow:hidden;">
        <div class="px-3 pt-3 pb-2">
            <div style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); border-radius:14px; padding:0.85rem;">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div style="font-size:0.72rem; color:rgba(255,255,255,0.75);">Access level</div>
                        <div class="fw-semibold text-white" style="font-size:0.92rem;">
                            {{ auth()->user()->isMainAdmin() ? 'Main Admin' : (auth()->user()->isSuperUser() ? 'Super User' : 'Support User') }}
                        </div>
                    </div>
                    <span class="badge bg-white text-primary" style="font-size:0.72rem;">{{ auth()->user()->department?->dept_name ?? 'Unassigned' }}</span>
                </div>
                <div class="mt-2" style="font-size:0.78rem; color:rgba(255,255,255,0.8);">
                    {{ auth()->user()->role?->ur_name ?? 'User' }} � {{ auth()->user()->isMainAdmin() ? 'Full control' : (auth()->user()->isSuperUser() ? 'Department scope' : 'Personal scope') }}
                </div>
            </div>
        </div>

        <div class="sidebar-section sidebar-scroll-hide" style="flex:1 1 auto; min-height:0; overflow-y:auto; overflow-x:hidden; scrollbar-width:none; -ms-overflow-style:none;">
            <ul class="nav nav-sidebar" id="navbar-nav" data-nav-type="accordion">
                <li class="nav-item-header">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Main</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link text-white">
                        <i class="ph-house" style="color:#fff;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link text-white">
                        <i class="ph-rows" style="color:#fff;"></i>
                        <span>{{ auth()->user()->isClientRep() ? 'My Tickets' : 'Tickets' }}</span>
                    </a>
                    <ul class="nav-group-sub collapse">
                        <li class="nav-item"><a href="{{ route('tickets.index') }}" class="nav-link">View Tickets</a></li>
                        <li class="nav-item"><a href="{{ route('tickets.create') }}" class="nav-link">New Ticket</a></li>
                    </ul>
                </li>

                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link text-white">
                        <i class="ph-arrow-fat-lines-down" style="color:#fff;"></i>
                        <span>Leave Requests</span>
                    </a>
                    <ul class="nav-group-sub collapse">
                        <li class="nav-item"><a href="{{ route('leaves.index') }}" class="nav-link">View Leave Forms</a></li>
                        <li class="nav-item"><a href="{{ route('leaves.create') }}" class="nav-link">Apply for Leave</a></li>
                    </ul>
                </li>

                @if(auth()->user()->isMainAdmin() || auth()->user()->isSuperUser())
                    <li class="nav-item-header mt-3">
                        <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Management</div>
                        <i class="ph-dots-three sidebar-resize-show"></i>
                    </li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link text-white">
                            <i class="ph-projector-screen" style="color:#fff;"></i>
                            <span>CRM Clients</span>
                        </a>
                        <ul class="nav-group-sub collapse">
                            <li class="nav-item"><a href="{{ route('clients.index') }}" class="nav-link">View CRM Clients</a></li>
                        </ul>
                    </li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link text-white">
                            <i class="ph-user-circle" style="color:#fff;"></i>
                            <span>Portal Clients</span>
                        </a>
                        <ul class="nav-group-sub collapse">
                            <li class="nav-item"><a href="{{ route('admin.external-clients.index') }}" class="nav-link">View Portal Clients</a></li>
                            <li class="nav-item"><a href="{{ route('admin.external-clients.create') }}" class="nav-link">Add Portal Client</a></li>
                            <li class="nav-item"><a href="{{ route('admin.messages.index') }}" class="nav-link">Client Messages</a></li>
                        </ul>
                    </li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link text-white">
                            <i class="ph-layout" style="color:#fff;"></i>
                            <span>Cost Center</span>
                        </a>
                        <ul class="nav-group-sub collapse">
                            <li class="nav-item"><a href="{{ route('departments.index') }}" class="nav-link">View Cost Centers</a></li>
                        </ul>
                    </li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link text-white">
                            <i class="ph-swatches" style="color:#fff;"></i>
                            <span>Users</span>
                        </a>
                        <ul class="nav-group-sub collapse">
                            <li class="nav-item"><a href="{{ route('users.index') }}" class="nav-link">View Users</a></li>
                            <li class="nav-item"><a href="{{ route('users.create') }}" class="nav-link">Add User</a></li>
                        </ul>
                    </li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link text-white">
                            <i class="ph-columns" style="color:#fff;"></i>
                            <span>User Roles</span>
                        </a>
                        <ul class="nav-group-sub collapse">
                            <li class="nav-item"><a href="{{ route('roles.index') }}" class="nav-link">View Roles</a></li>
                        </ul>
                    </li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link text-white">
                            <i class="ph-chart-bar" style="color:#fff;"></i>
                            <span>Issue Category</span>
                        </a>
                        <ul class="nav-group-sub collapse">
                            <li class="nav-item"><a href="{{ route('categories.index') }}" class="nav-link">Issue Categories</a></li>
                        </ul>
                    </li>

                    <li class="nav-item-header mt-3">
                        <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Reports</div>
                        <i class="ph-dots-three sidebar-resize-show"></i>
                    </li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link text-white">
                            <i class="ph-chart-bar" style="color:#fff;"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="nav-group-sub collapse">
                            <li class="nav-item"><a href="{{ route('reports.tickets') }}" class="nav-link">Ticket Report</a></li>
                            <li class="nav-item"><a href="{{ route('reports.clients') }}" class="nav-link">Client Report</a></li>
                            <li class="nav-item"><a href="{{ route('reports.leaves') }}" class="nav-link">Leave Report</a></li>
                            <li class="nav-item"><a href="{{ route('reports.users') }}" class="nav-link">User Report</a></li>
                        </ul>
                    </li>
                @endif

            </ul>

            {{-- Profile and session actions � always visible at bottom --}}
            <ul class="nav nav-sidebar mt-3 pt-2" id="navbar-nav-bottom" style="margin-top:auto !important; border-top:1px solid rgba(255,255,255,0.12);">
                <li class="nav-item">
                    <a href="{{ route('profile') }}" class="nav-link text-white">
                        <i class="ph-user-rectangle" style="color:#fff;"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="nav-link text-white border-0 bg-transparent w-100 text-start px-3">
                            <i class="ph-sign-out" style="color:#fff;"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- /main sidebar -->
