<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function hasRole(string $role, Chat $chat): bool
    {
        $pivot = $this->chats()->where('chat_id', $chat->id)->first()->pivot ?? null;
        return $pivot && $pivot->role === $role;
    }
    public function chats()
    {
        return $this->belongsToMany(Chat::class);
    }
    public function ownedChats()
    {
        return $this->hasMany(Chat::class);
    }

    public function avatar()
    {
        return $this->belongsTo(File::class);
    }
}
