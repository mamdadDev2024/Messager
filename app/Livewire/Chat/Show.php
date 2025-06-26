<?php

namespace App\Livewire\Chat;

use App\Models\Chat;
use Livewire\Component;

class Show extends Component
{
    public $chat;

    public function mount(Chat $Chat)
    {
        $this->chat = $Chat->load([
            'subscribers',
            'image',
            'owner',
            'messages.attachment',
            'messages.user'
        ]);

    }

    public function render()
    {
        return view('livewire.chat.show');
    }
}
