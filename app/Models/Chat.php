<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'image_id',
        'type'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
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
        return $this->belongsTo(File::class, 'image_id');
    }

    public function isVisibleTo(User $user)
    {
        return $this->user_id === $user->id ||
            $this->subscribers()->where('user_id', $user->id)->exists();
    }

    public function isOwn(User $user)
    {
        return $this->user_id === $user->id;
    }
}
