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
            'image' => [$this->method() == 'POST' ? 'required' : 'nullable', 'image', 'max:2048'],
            'short_description' => 'required|string',
            'description' => 'nullable|string',
            'teachers' => 'nullable|array',
            'teachers.*' => 'required|exists:users,id,role_id,' . User::ROLE_TEACHER,
        ];
    }

    public function attributes() : array
    {
        return [
            'name' => 'tiêu đề',
            'price' => 'giá',
            'image' => 'hình ảnh',
            'teachers' => 'giáo viên',
            'short_description' => 'mô tả ngắn',
            'description' => 'mô tả chi tiết'
        ];
    }
}
