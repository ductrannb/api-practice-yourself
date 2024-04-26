<?php

namespace App\Utils;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait Uploader
{
    public function storeFile($file, $path = Constants::DEFAULT_PATH, $oldUrl = null) : string
    {
        $url = $file->store($path);
        if ($oldUrl && File::exists(Storage::path($oldUrl))) {
            File::delete(Storage::path($oldUrl));
        }
        return $url;
    }
}
