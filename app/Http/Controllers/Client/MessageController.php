<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.client');
    }

    /**
     * Display messages for the client (only one view)
     */
    public function index(): View
    {
        $client = Auth::guard('client')->user();

        $messages = Message::where('external_client_id', $client->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread as read
        Message::where('external_client_id', $client->id)
            ->where('sent_by', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('client.messages.index', [
            'client' => $client,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message
     */
    public function send(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'message' => 'required|string|min:1|max:5000',
        ]);

        Message::create([
            'external_client_id' => $client->id,
            'sent_by' => 'client',
            'message' => $request->input('message'),
            'type' => 'general',
        ]);

        return back()->with('success', 'Message sent');
    }
}

