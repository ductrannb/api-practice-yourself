<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function register(array $data)
    {
        return $this->create($data);
    }

    public function getList($keyword = null)
    {
        return $this->model->when($keyword != null, function ($query) use ($keyword) {
            return $query->where('name', 'LIKE', '%' . $keyword . '%');
        });
    }
}
