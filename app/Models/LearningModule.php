<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model
{
    use HasFactory;

    const TYPE_CLASS = 1;
    const TYPE_CHAPTER = 2;
    const TYPE_UNIT = 3;

    public function parent()
    {
        return $this->belongsTo(LearningModule::class, 'parent_id');
    }
}
