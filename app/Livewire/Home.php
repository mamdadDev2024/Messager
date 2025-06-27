<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public $chats = [];
    public $search = '';

    public function mount()
    {
        $user = auth()->user();

        $owned = $user->ownedChats()
            ->with(['owner', 'image', 'latestMessage'])
            ->withCount(['messages'])
            ->get();

        $subscribed = $user->chats()
            ->with(['owner', 'image', 'latestMessage'])
            ->withCount(['messages'])
            ->get();

        $all = $owned->merge($subscribed)->unique('id')->values();

        $this->chats = $all;
    }

public function search()
{
    $search = strtolower($this->search);

    return collect($this->chats)->filter(function ($chat) use ($search) {
        return str_contains(strtolower($chat->title ?? ''), $search)
            || str_contains(strtolower($chat->owner->username ?? ''), $search);
    })->values();
}

    public function render()
    {
        return view('livewire.home');
    }
}
