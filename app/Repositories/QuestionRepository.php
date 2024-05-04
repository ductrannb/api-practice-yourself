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

    public function getList($lessonId, $keyword = null, $level = null)
    {
        return $this->model->where('lesson_id', $lessonId)
            ->when($keyword != null, function ($query) use ($keyword) {
                return $query->where('content', 'like', "%$keyword%");
            })
            ->when($level != null, function ($query) use ($level) {
                return $query->where('level', $level);
            })
            ->with(['choices', 'correctChoices', 'author'])
            ->paginate(10);
    }
}
