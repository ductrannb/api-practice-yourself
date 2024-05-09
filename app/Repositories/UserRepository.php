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

    public function getList($role_id, $keyword = null)
    {
        return $this->model
            ->where('role_id', '<>', User::ROLE_ADMIN)
            ->where('role_id', $role_id)
            ->when($keyword != null, function ($query) use ($keyword) {
                return $query->where('name', 'LIKE', '%' . $keyword . '%');
            })
            ->latest()
            ->orderByDesc('id')
            ->paginate(10);
    }
}
