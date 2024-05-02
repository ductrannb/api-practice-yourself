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
}
