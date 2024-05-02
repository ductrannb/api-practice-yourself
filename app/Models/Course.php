<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class Course extends BaseModel
{
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id')
            ->where('type', CourseUser::TYPE_TEACHER);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function assigned()
    {
        return $this->hasMany(CourseUser::class, 'course_id')->where('type', CourseUser::TYPE_TEACHER);
    }

    public function getImageUrlAttribute()
    {
        return env('APP_URL') . Storage::url($this->attributes['image']);
    }
}
