<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class File extends Model
{
    /** @use HasFactory<\Database\Factories\FileFactory> */
    use HasFactory , Prunable;

    protected $fillable = [
        'url',
        'size',
        'file_name',
        'type',
        'user_id',
        'visible'
    ];
}
