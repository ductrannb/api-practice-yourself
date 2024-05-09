<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'time' => 'required|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tiêu đề',
            'time' => 'thời gian'
        ];
    }
}
