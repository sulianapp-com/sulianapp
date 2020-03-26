<?php

namespace app\backend\modules\coupon\models;


class CouponLog extends \app\common\models\CouponLog
{
    //获取该公众号下所有的领取发放记录
    public static function getCouponLogs()
    {
        return static::uniacid()
                    ->select(['id', 'logno', 'member_id', 'couponid', 'getfrom', 'createtime'])
                    ->with(['member' => function($query){
                        return $query->select(['uid', 'nickname']);
                    }])
                    ->with(['coupon' => function($query){
                        return $query->select(['id', 'name']);
                    }])
                    ->orderBy('createtime', 'desc')
                    ->paginate(15);
    }

    //获取搜索的领取发放记录
    //array $searchData
    public static function searchCouponLog($searchData)
    {
        $res = static::uniacid()
                    ->select(['id', 'logno', 'member_id', 'couponid', 'getfrom', 'createtime'])
                    ->with(['member' => function($query){
                        return $query->select(['uid', 'nickname']);
                    }])
                    ->with(['coupon' => function($query){
                        return $query->select(['id', 'name']);
                    }]);

        if(isset($searchData['coupon_id'])){
            $res = $res->where('couponid', '=', $searchData['coupon_id']);
        }
        if(isset($searchData['coupon_name'])){
            $res = $res->whereHas('coupon', function($query) use ($searchData){
                return $query->where('name', 'like', '%'.$searchData['coupon_name'].'%');
            });
        }
        if(isset($searchData['nickname'])){
            $res = $res->whereHas('member', function($query) use ($searchData){
                return $query->where('nickname', 'like', '%'.$searchData['nickname'].'%');
            });
        }
        if(isset($searchData['get_from'])){
            $res = $res->where('getfrom', '=', $searchData['get_from']);
        }
        if($searchData['time_search_swtich'] == 1){
            $res = $res->whereBetween('createtime', [$searchData['time_start'], $searchData['time_end']]);
        }

        return $res->orderBy('createtime', 'desc')
            ->paginate(15);
    }


}