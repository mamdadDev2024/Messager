<?php

test('user can create a group and add members', function () {
    $owner = \App\Models\User::factory()->create();
    $member = \App\Models\User::factory()->create();
    $this->actingAs($owner);

    $response = $this->post('/livewire/message/chat.create-group', [
        'title' => 'گروه جدید',
        'users' => [$member->id],
    ]);

    $this->assertDatabaseHas('chats', [
        'title' => 'گروه جدید',
        'user_id' => $owner->id,
        'type' => 'group',
    ]);
    $chat = \App\Models\Chat::where('title', 'گروه جدید')->first();
    $this->assertTrue($chat->subscribers->contains($member));
}); 