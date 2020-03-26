<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/18
 * Time: 上午9:44
 */
namespace app\common\helpers\FormParam;


class TimeRange extends BaseFormParamType
{

    public function format($key,$value)
    {
        $timeRange = array_map(function ($time) {
            return substr($time,0,10);
        },$value);
        return $this->builder->whereBetween($key,$timeRange);
    }
}