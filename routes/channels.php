<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat/{id}', fn ($id) =>
    Chat::where('id', '=' , $id)->first()->exists()
);


