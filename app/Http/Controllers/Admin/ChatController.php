<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageSent;
use App\Events\TypingIndicator;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\SysUser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request): View
    {
        $query = Conversation::with(['latestMessage.sender', 'participants.participantable']);

        if ($request->filled('q')) {
            $query->where('subject', 'like', '%' . $request->input('q') . '%')
                ->orWhereHas('messages', function ($messageQuery) use ($request) {
                    $messageQuery->where('body', 'like', '%' . $request->input('q') . '%');
                });
        }

        return view('admin.chat.index', [
            'conversations' => $query->latest('updated_at')->paginate(20),
            'search' => $request->input('q'),
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $conversation->load(['messages.sender', 'participants.participantable']);

        return view('admin.chat.show', [
            'conversation' => $conversation,
        ]);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|min:1|max:5000',
        ]);

        $user = Auth::user();

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->user_id,
            'sender_type' => SysUser::class,
            'body' => $validated['body'],
            'status' => 'sent',
        ]);

        $conversation->touch();

        broadcast(new ChatMessageSent($message))->toOthers();

        return redirect()->route('admin.chat.show', $conversation)->with('success', 'Message sent.');
    }

    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        broadcast(new TypingIndicator($conversation, $user, 'admin'))->toOthers();

        return response()->json(['status' => 'typing']);
    }

    public function archive(Conversation $conversation): RedirectResponse
    {
        $conversation->update(['is_archived' => true]);

        return redirect()->route('admin.chat.index')->with('success', 'Conversation archived.');
    }
}
