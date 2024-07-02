<?php

namespace App\Repositories;

use App\Models\LearningModule;
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

    public function getList($keyword = null, $level = null, $learningModuleId = null, $learningModuleType = null, $paginate = true)
    {
        $query = $this->model
            ->when($keyword != null, function ($query) use ($keyword) {
                return $query->where('content', 'like', "%$keyword%");
            })
            ->when($level != null, function ($query) use ($level) {
                return $query->where('level', $level);
            })
            ->when($learningModuleId != null, function ($query) use ($learningModuleId, $learningModuleType) {
                return $query
                    ->when($learningModuleType == LearningModule::TYPE_CLASS, function ($query) use ($learningModuleId) {
                        return $query->whereHas('unit.parent', function ($query) use ($learningModuleId) {
                            return $query->where('parent_id', $learningModuleId);
                        });
                    })
                    ->when($learningModuleType == LearningModule::TYPE_CHAPTER, function ($query) use ($learningModuleId) {
                        return $query->whereHas('unit', function ($query) use ($learningModuleId) {
                            return $query->where('parent_id', $learningModuleId);
                        });
                    })
                    ->when($learningModuleType == LearningModule::TYPE_UNIT, function ($query) use ($learningModuleId) {
                        return $query->where('learning_module_id', $learningModuleId);
                    });
            })
            ->with(['choices', 'correctChoices', 'author', 'unit.parent.parent'])
            ->latest();
        if ($paginate) {
            return $query->paginate(10);
        }
        return $query->get();
    }
}
