<?php

namespace App\Models;

class QuestionChoice extends BaseModel
{
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
