<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory , Prunable , SoftDeletes;

    protected $fillable = [
        "text","user_id","chat_id","file_id","deleted_at"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function hasFile()
    {
        return isset($this->file);       
    }

    public function hasImage()
    {
        return $this->file->type == 'image';
    }

    public function isOwn(User $user)
    {
        return $this->id == $user->id;
    }
}
