<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/18
 * Time: 上午9:44
 */
namespace app\common\helpers\FormParam;


class Equal extends BaseFormParamType
{

    public function format($key,$value)
    {

        return $this->builder->where($key,$value);
    }
}