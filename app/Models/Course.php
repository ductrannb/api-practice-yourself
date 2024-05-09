<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Course extends BaseModel
{
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id')
            ->whereNull('course_user.deleted_at')
            ->where('type', CourseUser::TYPE_TEACHER);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id')
            ->whereNull('course_user.deleted_at')
            ->where('type', CourseUser::TYPE_USER);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function questions()
    {
        return $this->hasManyThrough(Question::class, Lesson::class, 'course_id', 'assignable_id')
            ->where('assignable_type', Question::TYPE_LESSON)
            ->latest();
    }

    public function assigned()
    {
        return $this->hasMany(CourseUser::class, 'course_id')->where('type', CourseUser::TYPE_TEACHER);
    }

    public function getImageUrlAttribute()
    {
        return Str::startsWith($this->attributes['image'], 'http')
            ? $this->attributes['image']
            : ($this->attributes['image'] ? env('APP_URL') . Storage::url($this->attributes['image']) : null);
    }

    public function getIsBoughtAttribute()
    {
        return $this->users->contains('id', auth()->id());
    }
}
