<?php

namespace App\Models;

class QuestionChoice extends BaseModel
{
    protected $casts = ['is_correct' => 'boolean'];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
