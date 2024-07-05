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
            'shuffle_questions' => 'required|boolean',
            'shuffle_choices' => 'required|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tiêu đề',
            'time' => 'thời gian',
            'shuffle_question' => 'xáo trộn câu hỏi',
            'shuffle_choices' => 'xáo trộn đáp án',
        ];
    }
}
