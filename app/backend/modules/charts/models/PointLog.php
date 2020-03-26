<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/10
 * Time: 下午5:47
 */


namespace app\backend\modules\charts\models;


class PointLog extends \app\common\models\finance\PointLog
{
    /**
     * @param $searchTime
     * @return mixed
     */
    public function getUsedCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->where('point_income_type', -1)->where('created_at', '<=', $searchTime)->sum('point') * -1;
        }
        return self::uniacid()->where('point_income_type', -1)->sum('point') * -1;
    }

    /**
     * @param $searchTime
     * @return mixed
     */
    public function getUseCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->where('created_at', '<=', $searchTime)->sum('point');
        }
        return self::uniacid()->sum('point');
    }

    /**
     * @param $searchTime
     * @return mixed
     */
    public function getGivenCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->where('point_income_type', 1)->where('created_at', '<=', $searchTime)->sum('point');
        }
        return self::uniacid()->where('point_income_type', 1)->sum('point');
    }
}