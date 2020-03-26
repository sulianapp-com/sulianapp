<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/22
 * Time: 17:37
 */

namespace app\backend\modules\charts\models;


use app\common\models\Withdraw;

class Income extends \app\common\models\Income
{

    public function scopeSearch($query,$search)
    {
        $query->uniacid();
        if ($search['member_id']) {
            $query->whereHas('hasOneMember',function ($q) use($search) {
                $q->whereUid($search['member_id']);
            });
        }
        if ($search['member']) {
            $query->whereHas('hasOneMember',function ($q) use($search) {
                $q->searchLike($search['member']);
            });
        }
        if ($search['is_time']) {
            $searchTime = [strtotime($search['time']['start']),strtotime($search['time']['end'])];
            $query->whereBetween('created_at', $searchTime);
        }

        return $query;

    }

    public function hasOneWithdraw()
    {
        return $this->hasOne(Withdraw::class, 'member_id', 'member_id');
    }

    public function hasOneMember()
    {
        return $this->hasOne(\app\common\models\Member::class, 'uid', 'member_id');
    }

}