<?php

namespace App\Repositories;

use App\Models\Exam;
use App\Models\User;

class ExamRepository extends BaseRepository
{
    public function __construct(Exam $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getList($keyword, $authorId = null)
    {
        return $this->model->when($keyword != null, function ($query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->when(
                auth()->user()->isRole(User::ROLE_ADMIN),
                function ($query) use ($authorId) {
                    if ($authorId != null) {
                        $query->where('author_id', $authorId);
                    }
                    return $query;
                },
                function ($query) {
                    return $query->where('user_id', auth()->id());
                }
            )
            ->latest()
            ->orderByDesc('id')
            ->with(['questions', 'author'])
            ->paginate(10);
    }
}
