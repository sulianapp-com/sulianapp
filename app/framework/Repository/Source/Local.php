<?php

namespace app\framework\Repository\Source;

use app\common\models\BaseModel;
use app\framework\Repository\RepositorySourceInterface;

class Local implements RepositorySourceInterface
{
    /**
     * @var array
     */
    private $all;
    private $items;
    private $model;

    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->items[$id];
    }

    /**
     * 按字段查找
     * @param $key
     * @param $ids
     * @return array
     */
    public function findManyBy($key, $ids)
    {
        $result = null;
        foreach ($this->items as $id => $item) {
            if (in_array($item[$key], $ids)) {
                $id = $item[$this->model->getKeyName()];
                $result[$id] = $item[$key];
            }
            if (count($result) == count($ids)) {
                //全部找到时终止
                break;
            }
        }
        return $result;
    }

    public function findBy($key, $id)
    {
        foreach ($this->items as $item) {
            if ($item[$key] == $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * 按id查找
     * @param $key
     * @param $ids
     * @return array
     */
    public function findMany($ids)
    {
        $result = null;
        foreach ($this->items as $id => $item) {
            if (in_array($id, $ids)) {
                $result[$id] = $item;
            }
            if (count($result) == count($ids)) {
                //全部找到时终止
                break;
            }
        }
        return $result;
    }

    public function all()
    {
        return $this->all;
    }

    public function save($id, $data)
    {
        $this->items[$id] = $data;
        return true;
    }

    public function saveMany($data)
    {
        $this->items = array_merge($this->items, $data);
    }

    public function saveBy($key, $id, $data)
    {
        $this->items[$this->model->getKeyName()] = $data;
    }

    public function saveManyBy($key, $data)
    {
        foreach ($data as $item) {
            $this->saveBy($key, $item[$key], $item);
        }
    }

    public function saveAll($data)
    {
        $this->all = $data;
        return true;
    }

    public function flush()
    {
        unset($this->items);
        unset($this->all);
        return true;
    }
}