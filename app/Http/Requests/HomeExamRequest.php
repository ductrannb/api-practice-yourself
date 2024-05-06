<?php

namespace App\Http\Requests;

class HomeExamRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'selected' => 'required|array',
            'selected.*.question_id' => 'required|exists:questions,id',
            'selected.*.question_choice_id' => 'required|exists:question_choices,id',
        ];
    }
}
