<?php

namespace App\Models;

class Lesson extends BaseModel
{
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
