<?php

namespace App\Http\Requests;

use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class QuestionRequest extends BaseRequest
{
    public function rules(): array
    {
        $rules = [
            'content' => 'required|string',
            'choices' => 'required|array|min:4|max:4',
            'choices.*.content' => 'required|string',
            'choices.*.is_correct' => 'required|boolean',
            'level' => ['required', Rule::in([Question::LEVEL_EASY, Question::LEVEL_MEDIUM, Question::LEVEL_HARD])],
            'assignable_id' => 'required|integer',
            'assignable_type' => ['required', Rule::in([Question::TYPE_LESSON, Question::TYPE_EXAM])],
            'solution' => 'nullable|string',
        ];
        if (!$this->isMethod('post')) {
            return Arr::except($rules, ['assignable_id']);
        }
        return $rules;
    }

    public function attributes(): array
    {
        return [
            'content' => 'nội dung câu hỏi',
            'level' => 'mức độ',
            'choices' => 'các đáp án',
            'solution' => 'lời giải'
        ];
    }
}
