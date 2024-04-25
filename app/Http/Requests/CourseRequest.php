<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'image' => 'required|image|max:2048',
            'teachers' => 'nullable|array',
            'teachers.*' => 'required|exists:users,id,role_id,' . User::ROLE_TEACHER,
        ];
    }

    public function attributes() : array
    {
        return [
            'name' => ''
        ];
    }
}
