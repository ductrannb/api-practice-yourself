<?php

namespace App\Models;

class ExamUser extends BaseModel
{
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function selected()
    {
        return $this->hasMany(QuestionChoiceSelected::class, 'assignable_id')
            ->where('assignable_type', QuestionChoiceSelected::TYPE_EXAM);
    }
}
