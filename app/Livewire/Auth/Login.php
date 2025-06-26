<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Title('Login')]
class Login extends Component
{
    #[Validate('required|email|exists:users')]
    public $email;
    #[Validate('required|string|min:6')]
    public $password;

    public function login()
    {
        Toaster::success('sdfasf ');
        $data = $this->validate();

        if (Auth::attempt($data , true))
        {
            Toaster::success('وارد شدید');
            return $this->redirectRoute('home');
        }else{
            Toaster::error('اطلاعات حساب کاربری شما پیدا نشد');
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
