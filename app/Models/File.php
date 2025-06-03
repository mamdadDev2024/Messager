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
        'type',
        'size',
        'url',
        'file_name'
    ];
    public static function booted()
    {
        static::deleting(function ($file) {
            $publicPath = public_path($file->url);
            if (file_exists($publicPath)) {
                unlink(asset($file->url));
            }
        });
    }
}
