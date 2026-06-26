<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Models\Ticket;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ClientTicketService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Client\AddCommentRequest;
use App\Http\Requests\Client\StoreTicketRequest;

/**
 * Client Ticket Controller
 * Handles ticket operations for external clients
 */
class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(ClientTicketService $ticketService)
    {
        $this->middleware('auth.client');
        $this->ticketService = $ticketService;
    }

    /**
     * Display list of client tickets
     */
    public function index(): View
    {
        $client = Auth::guard('client')->user();
        $filters = request()->only(['status', 'priority', 'search']);

        $tickets = $this->ticketService->getClientTickets($client, $filters);

        return view('client.tickets.index', [
            'tickets' => $tickets,
            'filters' => $filters,
        ]);
    }

    /**
     * Show create ticket form
     */
    public function create(): View
    {
        $categories = \App\Models\TicketCategory::where('is_active', 1)->get();
        return view('client.tickets.create', compact('categories'));
    }

    /**
     * Store new ticket
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        $data = $request->validated();

        $ticket = $this->ticketService->createTicket($client, $data);

        return redirect()->route('client.tickets.show', $ticket->ticket_id)
            ->with('success', 'Your support ticket has been created successfully. Ticket #' . $ticket->ticket_number . '. Our team will respond shortly.');
    }

    /**
     * Show ticket details
     */
    public function show(Ticket $ticket): View
    {
        $client = Auth::guard('client')->user();

        // Verify client owns this ticket
        $ticketData = $this->ticketService->getTicketForClientView($ticket->ticket_id, $client);

        if (!$ticketData) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $comments = $this->ticketService->getTicketComments($ticketData);

        return view('client.tickets.show', [
            'ticket' => $ticketData,
            'comments' => $comments,
        ]);
    }

    /**
     * Add comment to ticket
     */
    public function addComment(AddCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        // Verify client owns this ticket
        if ($ticket->external_client_id !== $client->id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $this->ticketService->addClientComment($ticket, $client, $request->input('message'));

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    /**
     * Upload attachment to ticket
     */
    public function uploadAttachment(Request $request, Ticket $ticket): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        // Verify client owns this ticket
        if ($ticket->external_client_id !== $client->id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $this->ticketService->uploadAttachment($ticket, $client, $request->file('file'));

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }

}
