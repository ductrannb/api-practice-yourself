<?php

namespace App\Models;

class UserSubject extends BaseModel
{
    protected $table = 'user_subject';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->hasMany(Subject::class);
    }
}
