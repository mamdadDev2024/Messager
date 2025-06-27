<?php

namespace App\Events;

use App\Models\File;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImageProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(public File $file)
    {
        $this->message = $file->message()->with(['attachment', 'chat'])->first();
        if (!$this->message || !$this->message->chat) {
            throw new \Exception("Message or related chat not found for File ID {$file->id}");
        }
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->toArray(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("chat.{$this->message->chat->id}"),
        ];
    }
}
