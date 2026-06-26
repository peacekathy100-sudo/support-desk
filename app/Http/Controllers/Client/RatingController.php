<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Models\TicketRating;
use App\Models\Ticket;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.client');
    }

    /**
     * Show rating form for closed ticket
     */
    public function create(Ticket $ticket): View
    {
        $client = Auth::guard('client')->user();

        if ($ticket->external_client_id !== $client->id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $existingRating = TicketRating::where('ticket_id', $ticket->ticket_id)
            ->where('client_id', $client->id)
            ->first();

        return view('client.ratings.create', [
            'ticket' => $ticket,
            'existingRating' => $existingRating,
        ]);
    }

    /**
     * Store ticket rating
     */
    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        if ($ticket->external_client_id !== $client->id) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        TicketRating::updateOrCreate(
            [
                'ticket_id' => $ticket->ticket_id,
                'client_id' => $client->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return redirect()->route('client.tickets.show', $ticket->ticket_id)
            ->with('success', 'Thank you for your feedback!');
    }
}
