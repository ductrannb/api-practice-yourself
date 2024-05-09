<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository extends BaseRepository
{
    public function __construct(Question $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getList($assignableId, $keyword = null, $level = null, $assignableType = null)
    {
        return $this->model->where('assignable_id', $assignableId)
            ->when($keyword != null, function ($query) use ($keyword) {
                return $query->where('content', 'like', "%$keyword%");
            })
            ->when($level != null, function ($query) use ($level) {
                return $query->where('level', $level);
            })
            ->when($assignableType != null, function ($query) use ($assignableType) {
                return $query->where('assignable_type', $assignableType);
            })
            ->with(['choices', 'correctChoices', 'author'])
            ->paginate(10);
    }
}
