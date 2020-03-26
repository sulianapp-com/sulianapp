<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/12/14
 * Time: 下午4:05
 */

namespace app\frontend\repositories;

abstract class Repository extends \Bosnadev\Repositories\Eloquent\Repository
{
    public function __call($name, $arguments)
    {
        if (method_exists($this->makeModel(), $name)) {
            return $this->makeModel()->$name(...$arguments);
        }
        throw new \Exception('不存在的方法'.$name);
        // TODO: Implement __call() method.
    }
}