<?php

namespace App\Repositories;

use App\Models\CourseUser;

class CourseUserRepository extends BaseRepository
{
    public function __construct(CourseUser $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }
}
