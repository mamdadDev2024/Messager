<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Title('Register')]
class Register extends Component
{
    #[Validate('required|email|unique:users')]
    public $email;
    #[Validate('required|string|unique:users')]
    public $username;
    #[Validate('required|string|min:6|confirmed')]
    public $password;
    public $password_confirmation;

    public function register()
    {
        $data = $this->validate();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if ($user instanceof Model)
        {
            Auth::login($user);
            Toaster::success('ثبت نام انجام شد');
            return $this->redirectRoute('home');
        }
        Toaster::error('مشکلی در عملیات رخ داد');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
