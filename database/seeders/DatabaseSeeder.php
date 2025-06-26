<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\File;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        $users->each(function (User $user) {

            $avatar = File::factory()->create(['user_id' => $user->id]);
            $user->avatar()->associate($avatar);
            $user->save();

            $chats = Chat::factory(10)->create([
                'user_id' => $user->id,
            ]);

            $chats->each(function (Chat $chat) {

                $chatImage = File::factory()->create(['user_id' => $chat->user_id]);
                $chat->image()->associate($chatImage);
                $chat->save();

                $messages = Message::factory(10)->create([
                    'user_id' => $chat->user_id,
                    'chat_id' => $chat->id,
                ]);

                $messages->each(function (Message $message) use ($chat) {

                    $attachment = File::factory()->create(['user_id' => $chat->user_id]);
                    $message->attachment()->associate($attachment);
                    $message->save();
                });
            });
        });
    }
}
