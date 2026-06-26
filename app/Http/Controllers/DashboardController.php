<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\SysUser;
use Illuminate\View\View;
use App\Models\AuditTrail;
use App\Models\LeaveRequest;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if (!$user instanceof SysUser) {
            abort(403, 'Authentication required.');
        }

        $isAdminOrSuperUser = $user->isMainAdmin() || $user->isSuperUser();

        // Build ticket query based on user role
        $ticketQuery = Ticket::query();
        if ($user->isMainAdmin()) {
            // Main admins see all tickets
        } elseif ($user->isSuperUser()) {
            $ticketQuery->forDepartment((int) $user->dept_id);
        } elseif ($user->isClientRep()) {
            $ticketQuery->forClient($user->client_id);
        } else {
            // Regular users see only their personal tickets
            $ticketQuery->forUser($user->user_id);
        }

        // Get all tickets at once and compute stats efficiently
        $allTickets = (clone $ticketQuery)->get();
        
        // Compute stats from in-memory collection (1 DB query instead of 11)
        $ticketStats = [
            'total'      => $allTickets->count(),
            'open'       => $allTickets->whereIn('status', ['open', 'in_progress', 'on_hold'])->count(),
            'resolved'   => $allTickets->where('status', 'resolved')->count(),
            'closed'     => $allTickets->where('status', 'closed')->count(),
            'overdue'    => $allTickets->filter(fn($t) => $t->due_at && $t->due_at < now() && !in_array($t->status, ['resolved', 'closed']))->count(),
            'chargeable' => $allTickets->where('chargeable', 1)->count(),
        ];

        $recentTickets = (clone $ticketQuery)
            ->with(['creator', 'category', 'client'])
            ->latest()
            ->take(5)
            ->get();

        // Get leave stats efficiently
        $leaveQuery = LeaveRequest::query();
        if (!$isAdminOrSuperUser) {
            $leaveQuery->where('user_id', $user->user_id);
        }
        
        $allLeaves = (clone $leaveQuery)->get();
        $leaveStats = [
            'total'    => $allLeaves->count(),
            'pending'  => $allLeaves->where('status', 'pending')->count(),
            'approved' => $allLeaves->where('status', 'approved')->count(),
            'rejected' => $allLeaves->where('status', 'rejected')->count(),
        ];

        // Personal work stats - for non-admin users only
        $myTicketStats = null;
        if (!$isAdminOrSuperUser && !$user->isClientRep()) {
            $uid = $user->user_id;
            $myTicketStats = [
                'solved'   => Ticket::where('resolved_by', $uid)->count(),
                'assigned' => Ticket::whereHas('assignees', fn($q) =>
                                  $q->where('ticket_assignees.user_id', $uid))->count(),
                'closed'   => Ticket::where('status', 'closed')
                                  ->where(function ($q) use ($uid) {
                                      $q->where('created_by', $uid)
                                        ->orWhereHas('assignees', fn($a) =>
                                            $a->where('ticket_assignees.user_id', $uid));
                                  })->count(),
            ];
        }

        // System-wide stats - only for admins/super users
        $clientStats = $isAdminOrSuperUser ? [
            'total'  => Client::count(),
            'active' => Client::where('is_active', 1)->count(),
        ] : [];

        $userStats = $isAdminOrSuperUser ? [
            'total'  => SysUser::count(),
            'online' => SysUser::where('user_online', 1)->count(),
            'active' => SysUser::where('user_status', 'active')->count(),
        ] : [];

        // Recent logins - only for admins/super users
        $recentLogins = [];
        if ($isAdminOrSuperUser) {
            $recentLoginsQuery = SysUser::whereNotNull('user_last_logged_in')
                ->with(['role', 'department']);

            if ($user->isSuperUser()) {
                $recentLoginsQuery->where('dept_id', $user->dept_id);
            }

            $recentLogins = $recentLoginsQuery
                ->orderByDesc('user_last_logged_in')
                ->take(5)
                ->get();
        }

        $departmentUsers = SysUser::where('dept_id', $user->dept_id)
            ->where('user_status', 'active')
            ->count();

        // Recent audits - only for admins/super users
        $recentAudits = $isAdminOrSuperUser
            ? AuditTrail::with('user')->orderByDesc('created_at')->take(6)->get()
            : collect();

        // Compute status breakdown from in-memory collection (1 DB query instead of 5)
        $ticketsByStatus = [
            'open'        => $allTickets->where('status', 'open')->count(),
            'in_progress' => $allTickets->where('status', 'in_progress')->count(),
            'on_hold'     => $allTickets->where('status', 'on_hold')->count(),
            'resolved'    => $allTickets->where('status', 'resolved')->count(),
            'closed'      => $allTickets->where('status', 'closed')->count(),
        ];

        // Compute monthly stats from in-memory collection (1 DB query instead of 2)
        $now       = now();
        $thisMonth = $allTickets->filter(function($t) use ($now) {
            return $t->created_at->month === $now->month && $t->created_at->year === $now->year;
        })->count();
        
        $lastMonth = $allTickets->filter(function($t) use ($now) {
            $prev = $now->copy()->subMonth();
            return $t->created_at->month === $prev->month && $t->created_at->year === $prev->year;
        })->count();

        $accessLabel = $user->isMainAdmin()
            ? 'Full system control'
            : ($user->isSuperUser()
                ? 'Department visibility'
                : ($user->isClientRep()
                    ? 'Client-only view'
                    : 'Personal workspace'));

        $accessTone = $user->isMainAdmin()
            ? 'success'
            : ($user->isSuperUser()
                ? 'primary'
                : ($user->isClientRep()
                    ? 'info'
                    : 'secondary'));

        return view('dashboard', compact(
            'ticketStats',
            'recentTickets',
            'leaveStats',
            'clientStats',
            'userStats',
            'recentLogins',
            'recentAudits',
            'ticketsByStatus',
            'thisMonth',
            'lastMonth',
            'myTicketStats',
            'departmentUsers',
            'accessLabel',
            'accessTone',
            'isAdminOrSuperUser'
        ));
    }
}
