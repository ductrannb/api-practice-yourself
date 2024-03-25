<?php

namespace App\Models;

class Subject extends BaseModel
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subject', 'user_id', 'subject_id');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}
