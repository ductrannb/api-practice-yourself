<?php

namespace App\Models;

use Symfony\Component\Console\Question\ChoiceQuestion;

class Question extends BaseModel
{
    const LEVEL_EASY = 1;
    const LEVEL_MEDIUM = 2;
    const LEVEL_HARD = 3;

    const TYPE_LESSON = 1;
    const TYPE_EXAM = 2;

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'assignable_id');
    }

    public function choices()
    {
        return $this->hasMany(QuestionChoice::class)->orderBy('id');
    }

    public function correctChoices()
    {
        return $this->hasMany(QuestionChoice::class)->where('is_correct', true);
    }

    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
