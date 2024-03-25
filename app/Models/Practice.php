<?php

namespace App\Models;

class Practice extends BaseModel
{
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function practiceQuestions()
    {
        return $this->hasMany(PracticeQuestion::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'practice_questions', 'practice_id', 'question_id');
    }

    public function setMessagesAtrribute($value)
    {
        if (!is_array($value)) {
            $value = [];
        }
        $this->attributes['messages'] = json_encode($value);
    }

    public function getMessagesAttribute()
    {
        return json_decode($this->attributes['messages'], true) ?? [];
    }
}
