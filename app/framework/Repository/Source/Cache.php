<?php

namespace app\framework\Repository\Source;

use app\framework\Repository\RepositorySourceInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Model;

class Cache implements RepositorySourceInterface
{
    /**
     * @var CacheManager
     */
    protected $cache;
    /**
     * @var mixed
     */
    protected $cacheTime;
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model, $cacheTime = null)
    {
        $this->model = $model;
        $this->tableName = $model->getTable();
        $this->cache = app('cache');
        $this->cacheTime = $cacheTime ?: config('cache.time', 1);
    }

    public function find($id)
    {
        return $this->cache->get("{$this->tableName}.find.{$id}");
    }

    public function findMany($ids)
    {
        $result = null;
        foreach ($ids as $id) {
            $item = $this->find($id);
            if ($item) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function findBy($key, $id)
    {
        return $this->cache->get("{$this->tableName}.findBy{$key}.{$id}");
    }

    public function findManyBy($key, $ids)
    {
        $result = null;
        foreach ($ids as $id) {
            $item = $this->findBy($key, $id);
            if ($item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function all()
    {
        return $this->cache->get("{$this->tableName}.all");
    }

    public function save($id, $data)
    {
        return $this->cache->put("{$this->tableName}.find.{$id}", $data, $this->cacheTime);
    }

    public function saveMany($data)
    {
        foreach ($data as $item) {
            $this->cache->put("{$this->tableName}.find.{$item[$this->model->getKeyName()]}", $item, $this->cacheTime);
        }
        return true;
    }

    public function saveBy($key, $id, $data)
    {
        return $this->cache->put("{$this->tableName}.findBy{$key}.{$id}", $data, $this->cacheTime);
    }

    public function saveManyBy($key, $data)
    {
        foreach ($data as $item) {
            $this->cache->put("{$this->tableName}.findBy{$key}.{$item[$key]}", $item, $this->cacheTime);
        }
        return true;
    }

    public function saveAll($data)
    {
        $result = $this->cache->put("{$this->tableName}.all", $data->toArray(), $this->cacheTime);
        return $result;
    }

    public function flush()
    {
        return $this->cache->forget("{$this->tableName}.all");
    }
}