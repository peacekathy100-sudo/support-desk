<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Events\ChatMessageSent;
use App\Events\TypingIndicator;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ExternalClient;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.client');
    }

    public function index(): View
    {
        $client = Auth::guard('client')->user();

        $conversations = Conversation::whereHas('participants', function ($query) use ($client) {
            $query->where('participantable_type', ExternalClient::class)
                ->where('participantable_id', $client->id);
        })
        ->with(['latestMessage.sender', 'participants.participantable'])
        ->latest('updated_at')
        ->paginate(20);

        return view('client.chat.index', [
            'conversations' => $conversations,
            'client' => $client,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $client = Auth::guard('client')->user();

        $isParticipant = $conversation->participants()->where('participantable_type', ExternalClient::class)
            ->where('participantable_id', $client->id)
            ->exists();

        abort_unless($isParticipant, 403);

        $conversation->load(['messages.sender', 'participants.participantable']);

        return view('client.chat.show', [
            'conversation' => $conversation,
            'client' => $client,
        ]);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        $isParticipant = $conversation->participants()->where('participantable_type', ExternalClient::class)
            ->where('participantable_id', $client->id)
            ->exists();

        abort_unless($isParticipant, 403);

        $validated = $request->validate([
            'body' => 'required|string|min:1|max:5000',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $client->id,
            'sender_type' => ExternalClient::class,
            'body' => $validated['body'],
            'status' => 'sent',
        ]);

        $conversation->touch();
        $conversation->participants()->where('participantable_type', ExternalClient::class)
            ->where('participantable_id', $client->id)
            ->update(['last_read_at' => now()]);

        broadcast(new ChatMessageSent($message))->toOthers();

        return redirect()->route('client.chat.show', $conversation);
    }

    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        $client = Auth::guard('client')->user();

        $isParticipant = $conversation->participants()->where('participantable_type', ExternalClient::class)
            ->where('participantable_id', $client->id)
            ->exists();

        abort_unless($isParticipant, 403);

        broadcast(new TypingIndicator($conversation, $client, 'client'))->toOthers();

        return response()->json(['status' => 'typing']);
    }
}
