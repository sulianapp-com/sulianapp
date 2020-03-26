<?php

namespace app\framework\Repository\Source;

use app\framework\Repository\RepositorySourceInterface;
use Illuminate\Database\Eloquent\Model;

class Eloquent implements RepositorySourceInterface
{
    /**
     * @var Model
     */
    private $model;
    /**
     * @var array
     */
    private $excludeFields;

    public function __construct(Model $model, $excludeFields = [])
    {
        $this->model = $model;
        $this->excludeFields = $excludeFields;
    }

    public function getBuilder()
    {
        $builder = $this->model;
        if ($this->excludeFields) {
            $builder = $builder->exclude($this->excludeFields);
        }
        return $builder;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findManyBy($key, $ids)
    {
        return $this->getBuilder()->getQuery()->whereIn($key, $ids)->get();
    }

    public function findBy($key, $id)
    {
        return $this->model->where($key, $id)->first($id);
    }

    public function all()
    {
        return $this->getBuilder()->getQuery()->get();
    }

    public function findMany($ids)
    {
        return $this->getBuilder()->getQuery()->whereIn($this->model->getKeyName(), $ids)->get();
    }

    public function save($id, $data)
    {
        return $this->getBuilder()->where($this->model->getKeyName(), $id)->update($data);
    }

    public function saveMany($data)
    {
        return true;
    }

    public function saveBy($key, $id, $data)
    {
        return true;
    }

    public function saveManyBy($key, $data)
    {
        return true;
    }

    public function saveAll($data)
    {
        return true;
    }

    public function flush()
    {
        return true;
    }
}