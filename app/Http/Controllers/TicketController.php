<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\SysUser;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\TicketCategory;
use App\Services\TicketService;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $service,
        private NotificationService $notifier
    ) {}

    public function index(Request $request): View
    {
        $user    = auth()->user();
        $query = Ticket::with(['creator', 'assignee', 'assignees', 'category']);

        if ($user->isClientRep()) {
            $query->forClient($user->client_id);
        } elseif ($user->isAdmin()) {
            // Admins see all
        } else {
            $query->forUser($user->user_id);
        }

        // Apply filters
        if ($request->status)   $query->where('status', $request->status);
        if ($request->priority) $query->byPriority($request->priority);
        if ($request->category) $query->where('category_id', $request->category);
        
        // Optimize search with proper indexing
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "{$search}%");
            });
        }

        // Cache categories
        $cacheKey = config('cache_config.ticket_categories.key', 'ticket_categories_active');
        $cacheTtl = config('cache_config.ticket_categories.ttl', 300);
        $categories = cache()->remember($cacheKey, $cacheTtl, fn () =>
            TicketCategory::where('is_active', 1)->get()
        );

        $tickets = $query->latest()->paginate(20);

        return view('tickets.index', compact('tickets', 'categories'));
    }

    public function create(): View
    {
        $categories = TicketCategory::where('is_active', 1)->get();
        $clients    = Client::where('is_active', 1)->orderBy('client_name')->get();
        $agents     = auth()->user()->isAgent()
            ? SysUser::with('role')
                ->whereHas('role', fn($q) =>
                    $q->where('permissions', 'like', '%"*"%')
                      ->orWhere('permissions', 'like', '%"tickets.*"%')
                      ->orWhere('permissions', 'like', '%"edit_tickets"%')
                )->where('user_status', 'active')->orderBy('user_surname')->get()
            : collect();
        return view('tickets.create', compact('categories', 'clients', 'agents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'subject'        => 'required|string|max:255',
            'description'    => 'required|string|min:10',
            'category_id'    => 'nullable|exists:ticket_categories,id',
            'priority'       => 'required|in:low,normal,high,urgent',
            'due_at'         => 'nullable|date|after:now',
            'attachments'    => 'nullable|array',
            'attachments.*'  => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt',
            'client_id'      => 'nullable|exists:clients,client_id',
            'chargeable'     => 'boolean',
            'notify_client'  => 'boolean',
            'agent_ids'      => 'nullable|array',
            'agent_ids.*'    => 'exists:sysuser,user_id',
        ]);

        if ($user->isClientRep()) {
            $data['client_id']     = $user->client_id;
            $data['agent_ids']     = [];
            $data['chargeable']    = 0;
            $data['notify_client'] = 0;
            $data['due_at']        = null;
        } else {
            $data['chargeable']    = $request->has('chargeable') ? 1 : 0;
            $data['notify_client'] = $request->has('notify_client') ? 1 : 0;
        }

        $data['attachments'] = $request->file('attachments') ?? [];

        $ticket = $this->service->create($data);

        return redirect()->route('tickets.show', $ticket)
                         ->with('success', "Ticket {$ticket->ticket_number} submitted successfully.");
    }

    public function uploadAttachment(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorizeView($ticket);

        $request->validate([
            'attachments'   => 'required|array|min:1',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt',
        ]);

        foreach ($request->file('attachments') as $file) {
            $this->service->attachFile($ticket, $file);
        }

        return back()->with('success', 'Attachment(s) uploaded successfully.');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorizeView($ticket);

        $ticket->load(['creator', 'client', 'assignee', 'assignees.role', 'category',
                       'comments.author', 'attachments.uploader', 'history.changer']);

        $agents = auth()->user()->isAgent()
            ? SysUser::where('user_status', 'active')->orderBy('user_surname')->get()
            : collect();

        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        // Only Main Admin can assign tickets to members
        if (!auth()->user()->isMainAdmin()) {
            abort(403, 'Only Main Admin can assign tickets to team members.');
        }

        $this->authorizeView($ticket);
        $request->validate([
            'agent_ids'   => 'required|array|min:1',
            'agent_ids.*' => 'exists:sysuser,user_id',
        ]);

        $this->service->assign($ticket, $request->agent_ids);

        $chargeable   = $request->has('chargeable') ? 1 : 0;
        $notifyClient = $request->boolean('notify_client');

        $ticket->update(['chargeable' => $chargeable]);

        if ($notifyClient && $ticket->client_id) {
            $ticket->refresh();
            $this->notifier->notifyClientChargeable($ticket);
        }

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorizeView($ticket);
        $request->validate([
            'status'          => 'required|in:open,in_progress,on_hold,resolved,closed',
            'resolution_note' => 'nullable|string',
        ]);
        $this->service->updateStatus($ticket, $request->status, $request->resolution_note);
        return back()->with('success', 'Status updated.');
    }

    public function comment(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorizeView($ticket);
        $request->validate(['comment' => 'required|string|min:2']);
        $isInternal = $request->boolean('is_internal') && auth()->user()->isAgent();
        $this->service->addComment($ticket, $request->comment, $isInternal);
        return back()->with('success', 'Comment added.');
    }

    public function updateChargeable(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can update the chargeable status.');
        }

        $chargeable   = $request->has('chargeable') ? 1 : 0;
        $notifyClient = $request->boolean('notify_client');

        $ticket->update(['chargeable' => $chargeable]);

        if ($notifyClient && $ticket->client_id) {
            $this->notifier->notifyClientChargeable($ticket);
        }

        $label = $chargeable ? 'marked as chargeable' : 'marked as not chargeable';
        $extra = ($notifyClient && $ticket->client_id) ? ' Client has been notified.' : '';
        return back()->with('success', "Ticket {$ticket->ticket_number} {$label}.{$extra}");
    }

    public function reopen(Ticket $ticket): RedirectResponse
    {
        $this->authorizeView($ticket);
        $this->service->reopen($ticket);
        return back()->with('success', 'Ticket reopened.');
    }

    private function authorizeView(Ticket $ticket): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) return;
        if ($ticket->created_by === $user->user_id) return;
        if ($ticket->assignees()->where('ticket_assignees.user_id', $user->user_id)->exists()) return;
        if ($user->isClientRep() && $ticket->client_id === $user->client_id) return;
        abort(403, 'You are not assigned to this ticket.');
    }
}
