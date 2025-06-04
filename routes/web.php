<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () =>
    view('welcome')
);


Route::as('chat.')->prefix('chat')->group(function () {
    
    Route::as('group.')->prefix('g')->group(function() { 
        Route::get('create' , [ChatController::class,'create'])->name('create');
        Route::post('delete' , [ChatController::class,'create'])->name('delete');
        Route::put('update/{Chat}' , [ChatController::class,'create'])->name('update');
        Route::get('{Chat}' , [ChatController::class,'create'])->name('show');
    });

    Route::as('channel.')->prefix('c')->group(function() {
        Route::get('create' , [ChatController::class,'create'])->name('create');
        Route::post('delete' , [ChatController::class,'create'])->name('delete');
        Route::put('update/{Chat}' , [ChatController::class,'create'])->name('update');
        Route::get('{Chat}' , [ChatController::class,'create'])->name('show');
    });
});