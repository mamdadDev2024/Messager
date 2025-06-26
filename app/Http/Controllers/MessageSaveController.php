<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MessageStoreRequest;
use App\Models\Message;

class MessageSaveController extends Controller
{
    public function save(MessageStoreRequest $request)
    {
        $data = $request->validated();

        $message = Message::create([
            'chat_id' => $data['chat_id'],
            'user_id' => auth()->id(),
            'text'    => $data['text'] ?? null,
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('chat_files' , 'public');

            $message->attachment()->create([
                'url' => $path,
                'user_id' => auth()->id(),
                'file_name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'type' => $request->file('file')->getMimeType(),
            ]);

            $message->load('attachment');
        }

        $message->load('user');

        broadcast(new MessageSent($message));

        return response()->json($message);
    }
}
