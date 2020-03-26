<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:24
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\member\models;


use app\common\models\member\BankCard;
use app\common\scopes\UniacidScope;

class MemberBankCard extends BankCard
{

    static protected $needLog = true;

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope( new UniacidScope);
    }



}
