<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|max:2048',
        ];
    }
}
