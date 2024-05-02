<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authentication;

class User extends Authentication implements JWTSubject
{
    use Notifiable;
    use HasFactory;
    use SoftDeletes;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_UNKNOWN = 3;

    const ROLE_USER = 1;
    const ROLE_TEACHER = 2;
    const ROLE_ADMIN = 3;

    protected $guarded = [];
    protected $hidden = ['password'];
    protected $casts = ['password' => 'hashed'];

    public function getAvatarUrlAttribute()
    {
        return Str::startsWith($this->attributes['avatar'], 'http')
            ? $this->attributes['avatar']
            : ($this->attributes['avatar'] ? Storage::url($this->attributes['avatar']) : null);
    }

    public function isRole($roleId) : bool
    {
        return $this->role_id === $roleId;
    }

    public function coursesAssigned()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id')
            ->where('type', CourseUser::TYPE_TEACHER);
    }

    public function coursesBought()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id')
            ->where('type', CourseUser::TYPE_USER);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
