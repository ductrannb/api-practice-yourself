<?php

namespace App\Models;

class Exam extends BaseModel
{
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
