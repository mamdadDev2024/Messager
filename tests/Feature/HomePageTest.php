<?php

test('home page shows user chats and supports search', function () {
    $user = \App\Models\User::factory()->create();
    $chat1 = \App\Models\Chat::factory()->create(['title' => 'گروه تست', 'user_id' => $user->id]);
    $chat2 = \App\Models\Chat::factory()->create(['title' => 'دوستان', 'user_id' => $user->id]);
    $chat2->subscribers()->attach($user);

    $this->actingAs($user);
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('گروه تست');
    $response->assertSee('دوستان');

    $response = $this->get(route('home', ['search' => 'دوستان']));
    $response->assertSee('دوستان');
});
