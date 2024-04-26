<?php

namespace App\Repositories;

use App\Models\Otp;

class LessonRepository extends BaseRepository
{
    public function __construct(Otp $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getList($keyword = null)
    {
        return $this->model
            ->when($keyword != null, function ($query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->latest()
            ->orderByDesc('id')
            ->paginate(10);
    }
}
