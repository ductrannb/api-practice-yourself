<?php

namespace App\Http\Requests;

class LessonImportQuestionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'pdf_id' => 'required|string',
            'lesson_id' => 'required|integer',
        ];
    }
}
