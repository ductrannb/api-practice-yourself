<?php

namespace App\Http\Requests;


class SelectChoiceRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'question_id' => 'required|exists:questions,id',
            'question_choice_id' => 'required|exists:question_choices,id',
        ];
    }
}
