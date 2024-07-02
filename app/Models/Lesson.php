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
        return $this->morphToMany(Question::class, 'assignable', 'question_mappings');
    }

    public function questionMappings()
    {
        return $this->hasMany(QuestionMapping::class, 'assignable_id')
            ->where('question_mappings.assignable_type', Lesson::class);
    }

    public function questionsSelected()
    {
        return $this->hasMany(QuestionChoiceSelected::class, 'sub_assignable_id')
            ->whereHas('courseUser', function ($query) {
                return $query->where('user_id', auth()->id());
            });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
