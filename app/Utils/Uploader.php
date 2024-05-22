<?php

namespace App\Utils;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
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

    public function uploadFile($file)
    {
        if (app()->isLocal()) {
            $response = Http::withHeader('api-practice-key', env('PRACTICE_API_KEY'))->post('https://dev-api.ductran.site/api/upload-file', [
                'file' => $file
            ]);
            if ($response->failed()) {
                return response()->json(['message' => 'Upload failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $response->json()['url'];
        }

        $path = $this->storeFile($file);
        return response()->json([ 'url' => Storage::url($path)]);
    }
}
