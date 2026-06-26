<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class TypingIndicator implements ShouldBroadcastNow
{
    use Batchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;
    public string $userName;
    public string $senderType;

    public function __construct(Conversation $conversation, object $user, string $senderType)
    {
        $this->conversation = $conversation;
        $this->senderType = $senderType;
        $this->userName = $user->full_name ?? ($user->user_name ?? 'Support');
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('conversation.' . $this->conversation->id);
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'user_name' => $this->userName,
            'sender_type' => $this->senderType,
            'typing' => true,
        ];
    }
}
