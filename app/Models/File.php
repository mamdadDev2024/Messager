<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class File extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'url',
        'size',
        'file_name',
        'type',
        'user_id',
        'visible',
        'processed'
    ];

    protected $casts = [
        'visible' => 'boolean',
        'processed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->hasMany(Message::class , 'attachment_id');
    }
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonth());
    }
}
