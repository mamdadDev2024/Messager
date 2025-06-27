<?php

namespace App\Livewire\Chat;

use App\Models\Chat;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Title('create group')]
class CreateGroup extends Component
{
    #[Validate('required|string|max:100')]
    public $title = '';
    #[Validate('array|min:1')]
    public $users = [];
    public $allUsers = [];

    public function mount()
    {
        $this->allUsers = User::where('id', '!=', auth()->id())->get();
    }

    public function create()
    {
        $this->validate();
        $chat = Chat::create([
            'title' => $this->title,
            'user_id' => auth()->id(),
            'type' => 'group',
        ]);
        $chat->subscribers()->sync($this->users);
        $this->reset(['title', 'users']);
        Toaster::success('گروه با موفقیت ایجاد شد');
        return $this->redirectRoute('chat.show' , ['Chat' => $chat->id]);
    }

    public function render()
    {
        return view('livewire.chat.create-group');
    }
}
