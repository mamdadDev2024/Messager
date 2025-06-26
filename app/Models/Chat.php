<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    /** @use HasFactory<\Database\Factories\ChatFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'file_id',
        'type'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'chat_user', 'chat_id', 'user_id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function image()
    {
        return $this->belongsTo(File::class);
    }
    public function isVisibleTo(User $user)
    {
        return $this->user_id === $user->id ||
            $this->subscribers()->where('user_id', $user->id)->exists();
    }
    public function isOwn(User $user)
    {
        return $this->owner->id === $user->id;
    }
}
