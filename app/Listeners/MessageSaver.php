<?php

namespace App\Listeners;

use App\Events\ImageProcessed;
use App\Events\MessageSent;
use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MessageSaver implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MessageSent $event): void
    {

        $file = File::find($event->message->attachment_id);
        if (!$file) return;

        if (!Str::startsWith($file->type, 'image/')) {
            return;
        }

        $filePath = public_path($file->url);
        if (!file_exists($filePath)) {
            \Log::warning("file not found!", ['path' => $filePath]);
            return;
        }


        $fileContents = file_get_contents($filePath);


        $response = Http::attach(
            'file',
            $fileContents,
            $file->file_name,
            ['Content-Type' => $file->type]
        )->post(env('FLASK_URL').'process-image');

        if ($response->successful()) {
            $file->visible = $response->json()['prediction'];
            $file->processed = true;
            $file->save();
            broadcast(new ImageProcessed($file));
        } else {
            \Log::error('error on sending image to API', [
                'file_id' => $file->id,
                'response' => $response->body(),
            ]);
        }
    }

}

