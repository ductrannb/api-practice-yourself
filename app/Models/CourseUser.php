<?php

namespace App\Models;

class CourseUser extends BaseModel
{
    const TYPE_USER = 1;
    const TYPE_TEACHER = 2;

    protected $table = 'course_user';
}
