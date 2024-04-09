<?php

namespace App\Models;

class Answer extends BaseModel
{
    public function questions()
    {
        return $this->hasMany(AnswerQuestion::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
