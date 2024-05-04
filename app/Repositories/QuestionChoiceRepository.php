<?php

namespace App\Repositories;

use App\Models\QuestionChoice;

class QuestionChoiceRepository extends BaseRepository
{
    public function __construct(QuestionChoice $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function createMany(array $choices)
    {
        return $this->model->insert($choices);
    }
}
