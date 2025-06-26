<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public $chats;

    public function mount()
    {
    $this->chats = auth()->user()->chats()
        ->with(['owner', 'messages', 'image' , 'latestMessage'])
        ->withCount(['messages'])
        ->get()
        ->toArray();
    }
    public function render()
    {
        return view('livewire.home');
    }
}
