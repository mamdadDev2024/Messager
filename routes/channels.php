<?php
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chat}', function (User $user, Chat $chat) {
    $user->load('avatar');

    $isMember = $user->chats()->where('chats.id', $chat->id)->exists()
        || $user->ownedChats()->where('chats.id', $chat->id)->exists();
    //  $isMember ?
    return [
        'id' => $user->id,
        'username' => $user->username,
        'avatar' => $user->avatar?->url,
    ];
});
