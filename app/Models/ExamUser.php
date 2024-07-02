<?php

namespace App\Models;

use App\Gemini\GeminiChat;

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

    public function getScoreAttribute()
    {
        return round($this->attributes['score'], 2) ?? 0;
    }

    public function geminiChat()
    {
        return $this->belongsTo(GeminiChat::class);
    }
}
