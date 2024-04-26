<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\User;

class CourseRepository extends BaseRepository
{
    public function __construct(Course $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getList($keyword = null)
    {
        $query = auth()->user()->isRole(User::ROLE_TEACHER)
            ? auth()->user()->coursesAssigned()
            : $this->model->query();
        return $query->when($keyword != null, function ($query) use ($keyword) {
                return $query->where('name', 'LIKE', '%' . $keyword . '%');
            })
            ->latest()
            ->orderByDesc('id')
            ->with(['teachers'])
            ->paginate(10);
    }
}
