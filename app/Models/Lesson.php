<?php

namespace App\Models;

class Lesson extends BaseModel
{
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
