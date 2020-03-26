<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/20 下午4:16
 * Email: livsyitian@163.com
 */

namespace app\frontend\models;


use app\common\scopes\MemberIdScope;
use app\common\scopes\UniacidScope;

class Income extends \app\common\models\Income
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope( new UniacidScope);
        self::addGlobalScope( new MemberIdScope);
    }

}
