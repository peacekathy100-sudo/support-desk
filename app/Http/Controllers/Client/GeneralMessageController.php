<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\GeneralMessage;
use App\Models\ExternalClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class GeneralMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.client');
    }

    /**
     * Show list of sent messages and admin replies
     */
    public function index(): View
    {
        $client = Auth::guard('client')->user();

        $messages = GeneralMessage::where('sender_id', $client->id)
            ->where('sender_type', ExternalClient::class)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.messages.index-new', [
            'messages' => $messages,
            'client' => $client,
        ]);
    }

    /**
     * Show compose message form
     */
    public function create(): View
    {
        return view('client.messages.create-new');
    }

    /**
     * Store new message
     */
    public function store(Request $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|min:10|max:5000',
        ]);

        GeneralMessage::create([
            'sender_id' => $client->id,
            'sender_type' => ExternalClient::class,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'status' => 'new',
        ]);

        return redirect()->route('client.inbox.index')
            ->with('success', 'Your message has been sent to HR and Admin. They will review and respond shortly.');
    }

    /**
     * Show message details and admin reply
     */
    public function show(GeneralMessage $message): View
    {
        $client = Auth::guard('client')->user();

        // Ensure client can only view their own messages
        if ($message->sender_id !== $client->id || $message->sender_type !== ExternalClient::class) {
            abort(403, 'Unauthorized');
        }

        // Mark as read if it's new
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        return view('client.messages.show-new', [
            'message' => $message,
            'client' => $client,
        ]);
    }
}
