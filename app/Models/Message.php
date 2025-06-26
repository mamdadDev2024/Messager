<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, Prunable, SoftDeletes;

    protected $fillable = [
        "text", "user_id", "chat_id", "file_id", "attachment_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function attachment()
    {
        return $this->belongsTo(File::class, 'attachment_id');
    }

    public function hasFile()
    {
        return $this->attachment !== null;
    }

    public function hasImage()
    {
        return $this->attachment && $this->attachment->type === 'image';
    }

    public function isOwn(User $user)
    {
        return $this->user_id == $user->id;
    }
}
