<?php

namespace App\Models;

class AnswerQuestion extends BaseModel
{
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function choicesSelected()
    {
        return $this->belongsToMany(
            QuestionChoice::class,
            'answer_question_choices_selected',
            'answer_question_id',
            'question_choice_id'
        );
    }

    public function correctChoicesSelected()
    {
        return $this->belongsToMany(
            QuestionChoice::class,
            'answer_question_choices_selected',
            'answer_question_id',
            'question_choice_id'
        )->where('is_correct', true);
    }

    public function incorrectChoicesSelected()
    {
        return $this->belongsToMany(
            QuestionChoice::class,
            'answer_question_choices_selected',
            'answer_question_id',
            'question_choice_id'
        )->where('is_correct', false);
    }
}
