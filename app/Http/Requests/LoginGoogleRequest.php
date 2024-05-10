<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginGoogleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'access_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
        ];
    }
}
