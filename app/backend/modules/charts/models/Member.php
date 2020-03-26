<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/19 上午11:30
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\models;


use app\common\scopes\UniacidScope;

class Member extends \app\backend\modules\member\models\Member
{

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope( new UniacidScope);
    }







}

