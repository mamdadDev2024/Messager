<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Chat;
use Livewire\Component;

class Search extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        $this->results = User::where('id', '!=', auth()->id())
            ->where('username', 'like', "%{$this->query}%")
            ->limit(10)
            ->get();
    }

    public function startChat($userId)
    {
        $user = User::findOrFail($userId);
        $chat = Chat::firstOrCreate([
            'type' => 'private',
            'user_id' => auth()->id(),
            'title' => null,
        ]);
        $chat->subscribers()->syncWithoutDetaching([$user->id, auth()->id()]);
        return redirect()->to('/chat/' . $chat->id);
    }

    public function render()
    {
        return view('livewire.user.search');
    }
} 