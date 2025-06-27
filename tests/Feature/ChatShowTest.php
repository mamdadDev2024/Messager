<?php

test('chat show page displays messages and members', function () {
    $owner = \App\Models\User::factory()->create();
    $member = \App\Models\User::factory()->create();
    $chat = \App\Models\Chat::factory()->create(['user_id' => $owner->id, 'type' => 'group']);
    $chat->subscribers()->attach([$owner->id, $member->id]);
    $message = \App\Models\Message::factory()->create([
        'chat_id' => $chat->id,
        'user_id' => $owner->id,
        'text' => 'سلام',
    ]);
    $this->actingAs($owner);
    $response = $this->get('/chat/' . $chat->id);
    $response->assertStatus(200);
    $response->assertSee('سلام');
    $response->assertSee($member->username);
}); 