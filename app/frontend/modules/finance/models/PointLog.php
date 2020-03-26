<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 下午2:39
 */

namespace app\frontend\modules\finance\models;


class PointLog extends \app\common\models\finance\PointLog
{
    /*public static function getPointTotal($member_id, $type = null)
    {
        $builder = PointLog::select()->byMemberId($member_id)->type($type)->limit(5)->orderBy('id', 'desc');
        return $builder;
    }

    public static function getLastTime($member_id, $type)
    {
        $time = PointLog::select('created_at')->byMemberId($member_id)->type($type)->first();
        return $time;
    }*/

    public static function getPointLogList($member_id, $type = null)
    {
        $builder = PointLog::select()->byMemberId($member_id)->type($type)->orderBy('id', 'desc');
        return $builder;
    }

    public function scopeByMemberId($query, $member_id)
    {
        return $query->where('member_id', $member_id)->uniacid();
    }

    public function scopeType($query, $type)
    {
        if (!isset($type) || $type == 0) {
            return $query;
        }
        return $query->where('point_income_type', $type);
    }
}