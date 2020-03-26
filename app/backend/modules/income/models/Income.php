<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/15 上午10:00
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\income\models;


use app\common\scopes\UniacidScope;

class Income extends \app\common\models\Income
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope( new UniacidScope);
    }


    public function scopeRecords($query)
    {
        return $query;
    }


    public function scopeWithMember($query)
    {
        return $query->with(['member' => function($query) {
            return $query->select('uid', 'nickname', 'realname', 'avatar', 'mobile');
        }]);
    }


    public function scopeSearch($query, $search)
    {
        if ($search['class']) {
            $query->where('incometable_type', $search['class']);
        }
        if ($search['status'] || $search['status'] == '0') {
            $query->where('status', $search['status']);
        }
        if ($search['pay_status'] || $search['pay_status'] == '0') {
            $query->where('pay_status', $search['pay_status']);
        }
        if ($search['search_time']) {
            $query = $query->whereBetween('created_at', [strtotime($search['time']['start']),strtotime($search['time']['end'])]);
        }
        return $query;
    }


    public function scopeSearchMember($query, $search)
    {
        if ($search['member_id'] || $search['realname']) {
            $query->whereHas('member', function($query)use($search) {
                if ($search['realname']) {
                    $query->select('uid', 'nickname','realname','mobile','avatar')
                        ->where('realname', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('mobile', 'like', '%' . $search['realname'] . '%')
                        ->orWhere('nickname', 'like', '%' . $search['realname'] . '%');
                }
                if ($search['member_id']) {
                    $query->whereUid($search['member_id']);
                }
            });
        }
        return $query;
    }

}
