<?php

namespace App\Livewire\Chat;

use App\Models\Chat;
use Livewire\Component;

class Show extends Component
{
    public $chat;
    public $showAddUser = false;
    public $addUserQuery = '';
    public $addUserResults = [];

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

    public function updatedAddUserQuery()
    {
        $this->addUserResults = \App\Models\User::where('id', '!=', auth()->id())
            ->where('username', 'like', "%{$this->addUserQuery}%")
            ->limit(10)
            ->get();
    }

    public function addUserToGroup($userId)
    {
        if ($this->chat->type !== 'group') return;
        $this->chat->subscribers()->syncWithoutDetaching([$userId]);
        $this->chat->refresh();
        $this->addUserQuery = '';
        $this->addUserResults = [];
        $this->showAddUser = false;
    }

    public function render()
    {
        return view('livewire.chat.show');
    }
}
