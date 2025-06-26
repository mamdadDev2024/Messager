<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MessageSaver implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MessageSent $event): void
    {
        $file = $event->message->attachment;

        if (!$file) return;

        if (!Str::startsWith($file->type, 'image/')) {
            return;
        }
        $filePath = public_path($file->url);
        if (!file_exists($filePath)) {
            \Log::warning("فایل در storage یافت نشد", ['path' => $filePath]);
            return;
        }

        $fileContents = file_get_contents($filePath);

        $response = Http::attach(
            'file',
            $fileContents,
            $file->file_name,
            ['Content-Type' => $file->type]
        )->post('http://localhost:5050/process-image');

        if ($response->successful()) {
            $file->visible = json_encode($response->json())['prediction'];
            $file->save();
        } else {
            \Log::error('خطا در ارسال تصویر به API', [
                'file_id' => $file->id,
                'response' => $response->body(),
            ]);
        }
    }
}

