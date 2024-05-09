<?php

namespace App\Models;

use Illuminate\Console\View\Components\Choice;

class QuestionChoiceSelected extends BaseModel
{
    const TYPE_COURSE = 1;
    const TYPE_EXAM = 2;

    protected $table = 'question_choice_selected';

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function courseUser()
    {
        return $this->belongsTo(CourseUser::class, 'assignable_id');
    }

    public function choice()
    {
        return $this->belongsTo(Choice::class);
    }
}
