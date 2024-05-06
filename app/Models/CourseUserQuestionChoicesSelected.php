<?php

namespace App\Models;

class CourseUserQuestionChoicesSelected extends BaseModel
{
    protected $table = 'course_user_question_choices_selected';

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function choice()
    {
        return $this->belongsTo(QuestionChoice::class);
    }

    public function getIsCorrectAttribute()
    {
        return $this->question->correctChoices->first()->id == $this->attributes['question_choice_id'] ?? false;
    }
}
