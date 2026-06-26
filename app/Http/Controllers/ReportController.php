<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\SysUser;
use Illuminate\View\View;
use App\Models\AuditTrail;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\TicketHistory;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function auditTrailForModel(string $model, iterable $models, int $limit = 8): Collection
    {
        $idList = $models instanceof Collection
            ? collect($models->modelKeys())
            : collect($models)
                ->map(fn ($m) => $m instanceof \Illuminate\Database\Eloquent\Model ? $m->getKey() : $m)
                ->filter(fn ($id) => filled($id))
                ->values();

        if ($idList->isEmpty()) {
            return collect();
        }

        return AuditTrail::where('model', $model)
            ->whereIn('model_id', $idList)
            ->with('user')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }

    private function auditTrailForTickets(iterable $tickets, int $limit = 8): Collection
    {
        $ticketIds = $tickets instanceof Collection
            ? collect($tickets->modelKeys())
            : collect($tickets)->pluck('ticket_id')->filter(fn ($value) => filled($value))->values();

        if ($ticketIds->isEmpty()) {
            return collect();
        }

        return TicketHistory::whereIn('ticket_id', $ticketIds)
            ->with('changer')
            ->orderByDesc('changed_at')
            ->take($limit)
            ->get();
    }

    private function getTicketQuery(Request $request)
    {
        $query = Ticket::with(['creator', 'assignee', 'category', 'client']);

        if ($request->status)     $query->where('status', $request->status);
        if ($request->priority)   $query->where('priority', $request->priority);
        if ($request->from_date)  $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->to_date)    $query->whereDate('created_at', '<=', $request->to_date);
        if ($request->client_id)  $query->where('client_id', $request->client_id);

        return $query->latest();
    }

    public function tickets(Request $request): View
    {
        $this->gate('view_reports');
        $query = $this->getTicketQuery($request);

        // Create a fresh query builder without the ordering for stats
        $statsQuery = Ticket::query();
        if ($request->status)     $statsQuery->where('status', $request->status);
        if ($request->priority)   $statsQuery->where('priority', $request->priority);
        if ($request->from_date)  $statsQuery->whereDate('created_at', '>=', $request->from_date);
        if ($request->to_date)    $statsQuery->whereDate('created_at', '<=', $request->to_date);
        if ($request->client_id)  $statsQuery->where('client_id', $request->client_id);

        $statsData = $statsQuery->selectRaw("
            count(*) as total,
            count(case when status in ('open', 'in_progress', 'on_hold') then 1 end) as open,
            count(case when status = 'resolved' then 1 end) as resolved,
            count(case when status = 'closed' then 1 end) as closed,
            count(case when chargeable = 1 then 1 end) as chargeable
        ")->first();

        $stats = $statsData ? $statsData->getAttributes() : ['total' => 0, 'open' => 0, 'resolved' => 0, 'closed' => 0, 'chargeable' => 0];
        $tickets = $query->get();
        $clients = Client::where('is_active', 1)->orderBy('client_name')->get();
        $auditTrail = $this->auditTrailForTickets($tickets);

        return view('reports.tickets', compact('tickets', 'stats', 'clients', 'auditTrail'));
    }

    public function exportTickets(Request $request): StreamedResponse
    {
        $this->gate('view_reports');
        $query = $this->getTicketQuery($request);
        $filename = 'tickets_report_' . now()->format('Y-m-d_Hi') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Ticket #', 'Subject', 'Priority', 'Status', 'Created By', 'Assigned To', 'Category', 'Client', 'Created At', 'Due At'];

        $callback = function() use ($columns, $query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(200, function ($tickets) use ($file) {
                foreach ($tickets as $ticket) {
                    fputcsv($file, [
                        $ticket->ticket_number,
                        $ticket->subject,
                        ucfirst($ticket->priority),
                        ucfirst(str_replace('_', ' ', $ticket->status)),
                        $ticket->creator?->full_name ?? 'N/A',
                        $ticket->assignee?->full_name ?? 'Unassigned',
                        $ticket->category?->name ?? 'N/A',
                        $ticket->client?->client_name ?? 'N/A',
                        $ticket->created_at->format('Y-m-d H:i'),
                        $ticket->due_at?->format('Y-m-d H:i') ?? 'N/A',
                    ]);
                }
            });

            fclose($file);
        };

        return ResponseFacade::stream($callback, 200, $headers);
    }

    private function getClientQuery(Request $request)
    {
        $query = Client::withCount('tickets');

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        if ($request->search) {
            $query->where(fn($q) =>
                $q->where('client_name', 'like', "%{$request->search}%")
                  ->orWhere('client_code', 'like', "%{$request->search}%")
                  ->orWhere('client_email', 'like', "%{$request->search}%")
            );
        }

        return $query->orderBy('client_name');
    }

    public function clients(Request $request): View
    {
        $this->gate('view_reports');
        $query = $this->getClientQuery($request);

        // Create a fresh query builder without the ordering for stats
        $statsQuery = Client::query();
        if ($request->is_active !== null && $request->is_active !== '') {
            $statsQuery->where('is_active', $request->is_active);
        }
        if ($request->search) {
            $statsQuery->where(fn($q) =>
                $q->where('client_name', 'like', "%{$request->search}%")
                  ->orWhere('client_code', 'like', "%{$request->search}%")
                  ->orWhere('client_email', 'like', "%{$request->search}%")
            );
        }

        $statsData = $statsQuery->selectRaw("
            count(*) as total,
            count(case when is_active = 1 then 1 end) as active,
            count(case when is_active = 0 then 1 end) as inactive
        ")->first();

        $stats = $statsData ? $statsData->getAttributes() : ['total' => 0, 'active' => 0, 'inactive' => 0];
        $clients = $query->get();
        $auditTrail = $this->auditTrailForModel('Client', $clients);

        return view('reports.clients', compact('clients', 'stats', 'auditTrail'));
    }

    public function exportClients(Request $request): StreamedResponse
    {
        $this->gate('view_reports');
        $query = $this->getClientQuery($request);
        $filename = 'clients_report_' . now()->format('Y-m-d_Hi') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Client Code', 'Client Name', 'Email', 'Contact', 'Address', 'Status', 'Total Tickets'];

        $callback = function() use ($columns, $query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(200, function ($clients) use ($file) {
                foreach ($clients as $client) {
                    fputcsv($file, [
                        $client->client_code,
                        $client->client_name,
                        $client->client_email ?? 'N/A',
                        $client->client_contact ?? 'N/A',
                        $client->client_address ?? 'N/A',
                        $client->is_active ? 'Active' : 'Inactive',
                        $client->tickets_count,
                    ]);
                }
            });

            fclose($file);
        };

        return ResponseFacade::stream($callback, 200, $headers);
    }

    private function getLeaveQuery(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'supervisor', 'approver']);

        if ($request->status)    $query->where('status', $request->status);
        if ($request->type)      $query->where('leave_type', $request->type);
        if ($request->from_date) $query->whereDate('from_date', '>=', $request->from_date);
        if ($request->to_date)   $query->whereDate('to_date', '<=', $request->to_date);

        return $query->latest();
    }

    public function leaves(Request $request): View
    {
        $this->gate('view_reports');
        $query = $this->getLeaveQuery($request);

        // Create a fresh query builder without the ordering for stats
        $statsQuery = LeaveRequest::query();
        if ($request->status)    $statsQuery->where('status', $request->status);
        if ($request->type)      $statsQuery->where('leave_type', $request->type);
        if ($request->from_date) $statsQuery->whereDate('from_date', '>=', $request->from_date);
        if ($request->to_date)   $statsQuery->whereDate('to_date', '<=', $request->to_date);

        $statsData = $statsQuery->selectRaw("
            count(*) as total,
            count(case when status = 'pending' then 1 end) as pending,
            count(case when status = 'approved' then 1 end) as approved,
            count(case when status = 'rejected' then 1 end) as rejected,
            COALESCE(sum(case when status = 'approved' then days_requested else 0 end), 0) as days
        ")->first();

        $stats = $statsData ? $statsData->getAttributes() : ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'days' => 0];
        $leaves = $query->get();
        $auditTrail = $this->auditTrailForModel('Leaverequest', $leaves);
        $leaveTypes = LeaveRequest::LEAVE_TYPES;

        return view('reports.leaves', compact('leaves', 'stats', 'leaveTypes', 'auditTrail'));
    }

    public function exportLeaves(Request $request): StreamedResponse
    {
        $this->gate('view_reports');
        $query = $this->getLeaveQuery($request);
        $filename = 'leaves_report_' . now()->format('Y-m-d_Hi') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Employee', 'Leave Type', 'From Date', 'To Date', 'Days Requested', 'Status', 'Supervisor', 'Approved By', 'Reason'];

        $callback = function() use ($columns, $query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(200, function ($leaves) use ($file) {
                foreach ($leaves as $leave) {
                    fputcsv($file, [
                        $leave->employee?->full_name ?? 'N/A',
                        $leave->leave_type ?? 'N/A',
                        $leave->from_date->format('Y-m-d'),
                        $leave->to_date->format('Y-m-d'),
                        $leave->days_requested,
                        ucfirst($leave->status),
                        $leave->supervisor?->full_name ?? 'N/A',
                        $leave->approver?->full_name ?? 'N/A',
                        $leave->reason ?? 'N/A',
                    ]);
                }
            });

            fclose($file);
        };

        return ResponseFacade::stream($callback, 200, $headers);
    }

    private function getUserQuery(Request $request)
    {
        $query = SysUser::with(['role', 'department']);

        if ($request->status) $query->where('user_status', $request->status);
        if ($request->role)   $query->where('user_role', $request->role);

        return $query->orderBy('user_surname');
    }

    public function users(Request $request): View
    {
        $this->gate('view_reports');
        $query = $this->getUserQuery($request);

        // Create a fresh query builder without the ordering for stats
        $statsQuery = SysUser::query();
        if ($request->status) $statsQuery->where('user_status', $request->status);
        if ($request->role)   $statsQuery->where('user_role', $request->role);

        $statsData = $statsQuery->selectRaw("
            count(*) as total,
            count(case when user_status = 'active' then 1 end) as active,
            count(case when user_status = 'inactive' then 1 end) as inactive,
            count(case when user_online = 1 then 1 end) as online
        ")->first();

        $stats = $statsData ? $statsData->getAttributes() : ['total' => 0, 'active' => 0, 'inactive' => 0, 'online' => 0];
        $users = $query->get();
        $auditTrail = $this->auditTrailForModel('SysUser', $users);
        $roles = \App\Models\UserRole::where('is_active', 1)->get();

        return view('reports.users', compact('users', 'stats', 'roles', 'auditTrail'));
    }

    public function exportUsers(Request $request): StreamedResponse
    {
        $this->gate('view_reports');
        $query = $this->getUserQuery($request);
        $filename = 'users_report_' . now()->format('Y-m-d_Hi') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Username', 'Full Name', 'Email', 'Telephone', 'Role', 'Department', 'Status', 'Online'];

        $callback = function() use ($columns, $query) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(200, function ($users) use ($file) {
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->user_name,
                        $user->full_name,
                        $user->user_email,
                        $user->user_telephone ?? 'N/A',
                        $user->role?->ur_name ?? 'N/A',
                        $user->department?->dept_name ?? 'N/A',
                        ucfirst($user->user_status),
                        $user->user_online ? 'Yes' : 'No',
                    ]);
                }
            });

            fclose($file);
        };

        return ResponseFacade::stream($callback, 200, $headers);
    }
}
