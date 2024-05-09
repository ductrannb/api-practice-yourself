<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->method() === 'POST') {
            return [
                'name' => 'required|string|max:255',
                'course_id' => 'required|integer|exists:courses,id',
            ];
        }
        return [
            'name' => 'required|string|max:255'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên bài học'
        ];
    }
}
