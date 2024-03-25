<?php

namespace App\Models;

class Grade extends BaseModel
{
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}
