<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionMapping extends BaseModel
{
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
