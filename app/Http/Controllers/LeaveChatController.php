<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveChatRequest;
use App\Models\Chat;
use Illuminate\Http\Request;
use Masmerise\Toaster\Toaster;

class LeaveChatController extends Controller
{
    public function __invoke(LeaveChatRequest $request)
    {
            $request->validated();
            try {
                $user = $request->user();
                $chatId = $request->input('chat_id');

                $chat = Chat::findOrFail($chatId);

                $chat->subscribers()->detach($user->id);

                Toaster::success('you leaved chat');
                return redirect()->route('home');
            } catch (\Throwable $th) {
                \Log::error($th->getMessage());
                Toaster::error('مشکلی در پس زمینه رخ داد');
                return redirect()->route('home');
            }
    }
}
