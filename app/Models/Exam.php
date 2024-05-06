<?php

namespace App\Models;

class Exam extends BaseModel
{
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'assignable_id')
            ->where('assignable_type', Question::TYPE_EXAM);
    }
}
