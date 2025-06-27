<?php

test('user can search for another user and start a chat', function () {
    $user1 = \App\Models\User::factory()->create(['username' => 'ali']);
    $user2 = \App\Models\User::factory()->create(['username' => 'reza']);
    $this->actingAs($user1);

    // جستجو
    $response = $this->post('/livewire/message/user.search', ['query' => 'reza']);
    $response->assertSee('reza');

    // شروع گفتگو
    $response = $this->post('/livewire/message/user.search', ['query' => 'reza', 'userId' => $user2->id, 'startChat' => true]);
    $chat = \App\Models\Chat::where('type', 'private')->first();
    $this->assertTrue($chat->subscribers->contains($user1));
    $this->assertTrue($chat->subscribers->contains($user2));
}); 