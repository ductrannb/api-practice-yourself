<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Utils\Uploader;

class UploadController extends Controller
{
    use Uploader;

    public function upload(UploadFileRequest $request)
    {
        $this->uploadFile($request);
    }
}
