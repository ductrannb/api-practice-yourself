<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|string|max:255',
            'new_password' => 'required|string|confirmed|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'password' => 'mật khẩu',
            'new_password' => 'mật khẩu mới'
        ];
    }
}
