<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['user' , 'attachment' , 'chat']);
        \Log::info($this->message->toArray());
    }

    public function broadcastWith(): array
    {
        return $this->message->toArray();
    }

    public function broadcastOn()
    {
        return new PresenceChannel("chat.{$this->message->chat->id}");
    }
}
