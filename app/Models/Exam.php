<?php

namespace App\Models;

class Exam extends BaseModel
{
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function histories()
    {
        return $this->hasMany(ExamUser::class, 'exam_id')->where('user_id' , auth()->id())->latest();
    }

    public function questions()
    {
        return $this->morphToMany(Question::class, 'assignable', 'question_mappings');
    }

    public function questionMappings()
    {
        return $this->hasMany(QuestionMapping::class, 'assignable_id')
            ->where('question_mappings.assignable_type', Exam::class);
    }
}
