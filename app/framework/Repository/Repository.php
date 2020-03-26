<?php

namespace app\framework\Repository;


use app\common\models\BaseModel;
use app\framework\Repository\Source\Cache;
use app\framework\Repository\Source\Eloquent;
use app\framework\Repository\Source\Local;

/**
 * Class Repository
 * @package app\framework\Repository
 */
abstract class Repository
{
    /**
     * @var []
     */
    static $instances = [];
    /**
     * @var array
     */
    private $source;
    protected $modelName;
    /**
     * @var BaseModel
     */
    protected $model;
    protected $cacheTime;
    protected $excludeFields = [];
    protected $only = [];

    private function getKeyName()
    {
        return $this->getModel()->getKeyName();
    }

    protected function getModel()
    {
        if (!isset($this->model)) {

            $this->model = new $this->modelName;
        }
        return $this->model;
    }

    protected function _getSource()
    {
        $model = $this->getModel();
        $source = [
            new Local($model),
            new Cache($model, $this->cacheTime),
            new Eloquent($model, $this->excludeFields),
        ];
        return $source;
    }

    private function getSource()
    {
        if (!isset($this->source)) {
            $this->source = $this->_getSource();
        }
        return $this->source;
    }

    protected function findManyBy($key,$ids)
    {
        $data = new Collection();
        // 记录每层被穿透的数据
        $missedSourcesIds = [];
        // 逐层向上查找
        foreach ($this->getSource() as $source) {
            /**
             * @var RepositorySourceInterface $source
             */
            $data = $source->findMany($key,$ids);

            $findIds = array_column($data, $key) ?: [];

            $missedIds = array_diff($ids, $findIds);

            if (!count($missedIds)) {
                // 全部找到,终止循环
                break;
            }
            // 记录没找到的
            $missedSourcesIds[] = [
                'source' => $source,
                'ids' => $missedIds,
            ];
        }

        // 保存数据到被穿透的层
        if ($missedSourcesIds) {
            // 每个穿透的层
            foreach ($missedSourcesIds as $missedSourceItemIds) {
                // 这层被穿透的数据
                $missedData = [];
                foreach ($data as $item) {
                    // 根据id匹配对应的数据
                    if (in_array($item[$key], $missedSourceItemIds['ids'])) {
                        $missedData[] = $item;
                    }
                }

                /**
                 * @var RepositorySourceInterface $missedSource
                 */
                $missedSourceItemIds['source']->saveMany($missedData);
            }
        }

        return new Collection($data);
    }

    /**
     *
     * @param $ids
     * @return Collection
     */
    protected function findMany($ids)
    {
        return $this->findManyBy($this->getKeyName(),$ids);
    }

    /**
     * @param $id
     * @return null
     */
    protected function find($id)
    {
        $data = null;
        $missedSources = [];
        // 逐层向上查找
        foreach ($this->getSource() as $source) {
            /**
             * @var RepositorySourceInterface $source
             */
            $data = $source->find($id);
            if (isset($data)) {
                break;
            }
            $missedSources[] = $source;
        }

        // 保存到被穿透的层
        if ($missedSources) {

            foreach ($missedSources as $missedSource) {
                /**
                 * @var RepositorySourceInterface $missedSource
                 */
                $missedSource->save($id, $data);
            }
        }

        return $data;
    }

    /**
     * @return null
     */
    protected function all()
    {
        $data = new Collection();
        $missedSources = [];
        // 逐层向上查找
        foreach ($this->getSource() as $source) {
            /**
             * @var RepositorySourceInterface $source
             */
            $data = $source->all();
            if (isset($data)) {
                break;
            }
            $missedSources[] = $source;
        }

        // 保存到被穿透的层
        if ($missedSources) {

            foreach ($missedSources as $missedSource) {
                /**
                 * @var RepositorySourceInterface $missedSource
                 */
                $missedSource->saveAll($data);
            }
        }

        return new Collection($data);
    }

    protected function flush()
    {
        foreach ($this->getSource() as $source) {
            $source->flush();
        }
    }

    public function __call($name, $arguments)
    {
        if (!self::$instances[static::class]) {
            self::$instances[static::class] = $this;
        }
        if (method_exists($this, $name)) {
            return self::$instance['static::class']->{$name}(...$arguments);
        }

    }

    public static function __callStatic($name, $arguments)
    {

        if (!self::$instances[static::class]) {
            self::$instances[static::class] = new static();
        }

        return self::$instances[static::class]->{$name}(...$arguments);

    }
}