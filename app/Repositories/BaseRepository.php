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

    public function update($id, $data)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new RecordsNotFoundException();
        }
        $record->update($data);
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

    public function find($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new RecordsNotFoundException();
        }
        return $record;
    }
}
