<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MessageStoreRequest;
use App\Models\File;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class MessageSaveController extends Controller
{
    public function __invoke(MessageStoreRequest $request)
    {
        $data = $request->validated();

        $message = Message::create([
            'chat_id' => $data['chat_id'],
            'user_id' => auth()->id(),
            'text'    => $data['text'] ?? null,
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('chat_files', 'public');

            $file = File::create([
                'url' => $path,
                'user_id' => auth()->id(),
                'file_name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'type' => $request->file('file')->getMimeType(),
            ]);

            $message->attachment_id = $file->id;
            $message->save();
        }
        broadcast(new MessageSent($message));
        $message->load(['attachment', 'user']);
        return response()->json(['message' => $message]);
    }
}
