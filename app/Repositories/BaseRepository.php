<?php

namespace App\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\RecordsNotFoundException;

abstract class BaseRepository
{
    protected $model;

    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    /**
     * @throws BindingResolutionException
     */
    public function setModel()
    {
        $this->model = app()->make($this->getModel());
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->model->updateOrCreate($conditions, $data);
    }

    public function update($id, $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new RecordsNotFoundException();
        }
        $record->fill($data);
        $record->save();
        return $record;
    }

    public function updateWithConditions(array $conditions, $data)
    {
        $record = $this->firstOfWhere($conditions);
        $record->fill($data);
        $record->save();
        return $record;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new RecordsNotFoundException();
        }
        return $record->delete();
    }

    public function find($id, array $relations = [])
    {
        $record = $this->model->with($relations)->find($id);
        if (!$record) {
            throw new RecordsNotFoundException();
        }
        return $record;
    }

    public function firstOfWhere(array $conditions)
    {
        $record = $this->model->where($conditions)->first();
        if (!$record) {
            throw new RecordsNotFoundException();
        }
        return $record;
    }

    public function firstOfWhereNotFail(array $conditions)
    {
        return $this->model->where($conditions)->first();
    }
    public function latestOfWhere(array $conditions)
    {
        $record = $this->model->where($conditions)->latest()->first();
        if (!$record) {
            throw new RecordsNotFoundException();
        }
        return $record;
    }

    public function where(array $conditions)
    {
        return $this->model->where($conditions)->get();
    }
}
