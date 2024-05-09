<?php

namespace App\Models;

class Lesson extends BaseModel
{
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'assignable_id')->where('assignable_type', Question::TYPE_LESSON);
    }

    public function questionsSelected()
    {
        return $this->hasManyThrough(QuestionChoiceSelected::class, Question::class, 'assignable_id', 'question_id')
            ->where('question_choice_selected.assignable_type', QuestionChoiceSelected::TYPE_COURSE)
            ->whereHas('courseUser', function ($query) {
                return $query->where('user_id', auth()->id());
            });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
