<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/31
 * Time: 下午4:40
 */

namespace app\common\models;


class Modules extends BaseModel
{
    public $table = 'modules';

    public static function getModuleName($name)
    {
        return self::where('name', $name)
            ->where('type', 'biz')
            ->first();
    }

    public static function getModuleInfo($name)
    {
        return self::select('name', 'title', 'version')->where('name', $name)
            ->where('type', 'biz')
            ->first();
    }
}