<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat/{type}/{id}', fn ($type, $id) =>
    Chat::where('id', '=' , $id , 'and' , 'type' , $type)->first()->exists()
);


