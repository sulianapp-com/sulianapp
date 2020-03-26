<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-07-08
 * Time: 10:31
 */

namespace app\backend\models\excelRecharge;


use app\common\scopes\UniacidScope;

class DetailModel extends \app\common\models\excelRecharge\DetailModel
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }
}
