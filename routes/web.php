<?php

use App\Http\Controllers\MessageSaveController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Chat\Show;
use App\Livewire\Home;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get("/", Home::class)->middleware('auth')->name('home');

Route::prefix('auth')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::post('logout', function (){
        Auth::logout();
        return redirect()->route('home');
    })->middleware('auth')->name('logout');
});

Route::as('chat.')->prefix('chat')->middleware('auth')->group(function () {
    Route::get('{Chat}' , Show::class)->name('show');
    Route::post('{Chat}/message' , [MessageSaveController::class , 'save'])->name('message.save');
});
