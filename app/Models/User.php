<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'avatar_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class)->withPivot('role');
    }

    public function ownedChats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }

    public function avatar()
    {
        return $this->belongsTo(File::class, 'avatar_id');
    }

    public function hasRole(string $role, Chat $chat): bool
    {
        $chatRelation = $this->chats()->where('chat_id', $chat->id)->first();
        $pivot = $chatRelation ? $chatRelation->pivot : null;
        return $pivot && $pivot->role === $role;
    }
}
