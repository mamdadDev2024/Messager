<?php

namespace App\Observers;

use App\Models\File;

class FileObserver
{
    public function deleted(File $file): void
    {
        $publicPath = public_path($file->url);
        if (file_exists($publicPath)) {
            unlink(asset($file->url));
        }
    }
}
