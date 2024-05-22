<?php

namespace App\Utils;

use App\Http\Requests\UploadFileRequest;
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

    public function uploadFile(UploadFileRequest $request)
    {
        if (!$request->file) {
            return response()->json(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }
        $file = $request->file;
        if (app()->isLocal()) {
            $response = Http::withHeader('api-practice-key', env('PRACTICE_API_KEY'))->post('https://api-dev.ductran.site/api/upload-file', [
                'file' => $file
            ]);
            if ($response->failed()) {
                return response()->json(['message' => 'Upload failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $response->json()['url'];
        }
        if ($request->header('api-practice-key') != env('PRACTICE_API_KEY')) {
            return response()->json(['message' => 'You do not have access'], Response::HTTP_FORBIDDEN);
        }
        $path = $this->storeFile($file);
        return response()->json([ 'url' => Storage::url($path)]);
    }
}
