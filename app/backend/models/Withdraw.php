<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/27 下午4:11
 * Email: livsyitian@163.com
 */

namespace app\backend\models;


use app\common\scopes\UniacidScope;

class Withdraw extends \app\common\models\Withdraw
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope);
    }

    public function scopeRecords($query)
    {
        $query->with(['hasOneMember' => function ($query) {
            return $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);

        return parent::scopeRecords($query);
    }

    public function scopeSearch($query, $search)
    {
        if ($search['member_id']) {
            $query->where('member_id', $search['member_id']);
        }
        if (isset($search['status']) && $search['status'] != "") {
            $query->ofStatus($search['status']);
        }
        if ($search['withdraw_sn']) {
            $query->ofWithdrawSn($search['withdraw_sn']);
        }
        if ($search['type']) {
            $query->whereType($search['type']);
        }
        if ($search['pay_way']) {
            $query->where('pay_way', $search['pay_way']);
        }
        if ($search['searchtime']) {
            $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
            $query->whereBetween('created_at', $range);
        }
        if ($search['member']) {
            $query->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }
        return $query;
    }

    public static function getTypes()
    {
        $configs = \app\backend\modules\income\Income::current()->getItems();

        return $configs;
    }
}
