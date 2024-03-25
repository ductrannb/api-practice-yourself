<?php

namespace App\Models;

class PracticeQuestion extends BaseModel
{
    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function choicesSelected()
    {
        return $this->belongsToMany(
            QuestionChoice::class,
            'practice_question_choices_selected',
            'practice_question_id',
            'question_choice_id'
        );
    }

    public function correctChoicesSelected()
    {
        return $this->belongsToMany(
            QuestionChoice::class,
            'practice_question_choices_selected',
            'practice_question_id',
            'question_choice_id'
        )->where('is_correct', true);
    }

    public function incorrectChoicesSelected()
    {
        return $this->belongsToMany(
            QuestionChoice::class,
            'practice_question_choices_selected',
            'practice_question_id',
            'question_choice_id'
        )->where('is_correct', false);
    }
}
