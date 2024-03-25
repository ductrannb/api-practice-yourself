<?php

namespace App\Models;

class Exam extends BaseModel
{
    const TYPE_PRACTICE_TEST = 1;
    const TYPE_ASSESSMENT = 2;

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
