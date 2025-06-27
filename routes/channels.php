<?php
use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chat}', function ($user, Chat $chat) {
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
