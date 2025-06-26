<?php

namespace App\Livewire\Chat;

use App\Models\Chat;
use Livewire\Component;

class Show extends Component
{
    public $chat;

    public function mount(Chat $Chat)
    {
        $this->chat = $Chat->with(['subscribers', 'image', 'owner', 'messages.attachment'])->first()->toArray();
    }

    public function render()
    {
        return view('livewire.chat.show', [
            'chat' => $this->chat,
        ]);
    }
}
