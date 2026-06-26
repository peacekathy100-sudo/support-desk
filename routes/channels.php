<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Models\Conversation;
use App\Models\ExternalClient;
use App\Models\SysUser;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::with('participants')->find($conversationId);
    if (!$conversation) {
        return false;
    }

    $match = false;

    if ($user instanceof SysUser) {
        $match = $conversation->participants->contains(function ($participant) use ($user) {
            return $participant->participantable_type === SysUser::class
                && $participant->participantable_id === $user->user_id;
        });
    }

    if ($user instanceof ExternalClient) {
        $match = $conversation->participants->contains(function ($participant) use ($user) {
            return $participant->participantable_type === ExternalClient::class
                && $participant->participantable_id === $user->id;
        });
    }

    return $match;
});
