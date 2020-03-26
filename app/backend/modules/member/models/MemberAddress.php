<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/12/29 下午3:52
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\member\models;


use app\common\scopes\UniacidScope;

class MemberAddress extends \app\common\models\MemberAddress
{
    /**
     * 添加全局作用域
     */
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope( new UniacidScope);
    }


    /**
     * 记录检索
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query;
    }


    /**
     * 检索条件 会员ID uid
     * @param $query
     * @param $uid
     * @return mixed
     */
    public function scopeOfUid($query, $uid)
    {
        return $query->where('uid', $uid);
    }

}
