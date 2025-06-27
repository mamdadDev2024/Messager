<?php

test('user model relationships', function () {
    $user = \App\Models\User::factory()->create();
    $chat = \App\Models\Chat::factory()->create(['user_id' => $user->id]);
    $user->chats()->attach($chat);
    $message = \App\Models\Message::factory()->create(['chat_id' => $chat->id, 'user_id' => $user->id]);

    expect($user->ownedChats)->toContain($chat);
    expect($user->chats)->toContain($chat);
    expect($user->messages)->toContain($message);
}); 