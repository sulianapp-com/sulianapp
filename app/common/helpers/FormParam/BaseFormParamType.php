<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/18
 * Time: 上午9:43
 */

namespace app\common\helpers\FormParam;

use app\framework\Database\Eloquent\Builder;

abstract class BaseFormParamType
{
    protected $builder;
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    abstract public function format($key,$value);
}