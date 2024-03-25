<?php

namespace App\Models;

class Question extends BaseModel
{
    const LEVEL_EASY = 1;
    const LEVEL_MEDIUM = 2;
    const LEVEL_HARD = 3;

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function choices()
    {
        return $this->hasMany(QuestionChoice::class);
    }
}
