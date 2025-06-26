<?php
use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{Chat}', function ($user, Chat $Chat) {
    return $user->chats()->where('id', $Chat->id)->exists()
        || $user->ownedChats()->where('id', $Chat->id)->exists();
});
