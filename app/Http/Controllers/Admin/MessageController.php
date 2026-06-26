<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\ExternalClient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show all conversations
     */
    public function index(): View
    {
        $conversations = ExternalClient::with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }])
            ->whereHas('messages')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.messages.index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Show messages for a specific client
     */
    public function show($clientId): View
    {
        $client = ExternalClient::findOrFail($clientId);
        
        $messages = Message::where('external_client_id', $clientId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        Message::where('external_client_id', $clientId)
            ->where('sent_by', 'client')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.messages.show', [
            'client' => $client,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message from admin
     */
    public function send(Request $request, $clientId)
    {
        $request->validate([
            'message' => 'required|string|min:1|max:5000',
        ]);

        Message::create([
            'external_client_id' => $clientId,
            'sent_by' => 'admin',
            'sent_by_user_id' => Auth::user()->user_id,
            'message' => $request->input('message'),
            'type' => 'general',
            'is_read' => false,
        ]);

        return back()->with('success', 'Message sent');
    }
}

