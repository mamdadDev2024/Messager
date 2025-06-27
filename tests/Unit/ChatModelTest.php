<?php

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;

test('chat model relationships', function () {
    $user = User::factory()->create();
    $chat = Chat::factory()->create(['user_id' => $user->id]);

    $member = User::factory()->create();
    $chat->subscribers()->attach($member);

    $message = Message::factory()->create([
        'chat_id' => $chat->id,
        'user_id' => $user->id
    ]);

    $chat->refresh()->load(['owner', 'subscribers', 'messages']);

    expect($chat->owner)->toBeInstanceOf(User::class);
    expect($chat->subscribers->pluck('id'))->toContain($member->id);
    expect($chat->messages->pluck('id'))->toContain($message->id);
});
