<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/11/1
 * Time: 9:53 AM
 */

namespace app\backend\modules\role\models;


use app\common\scopes\UniacidScope;

class RoleModel extends \app\common\models\role\RoleModel
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope( new UniacidScope);
    }

}
