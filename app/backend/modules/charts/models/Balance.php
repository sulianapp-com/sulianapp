<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/14
 * Time: 18:43
 */

namespace app\backend\modules\charts\models;


class Balance extends \app\common\models\finance\Balance
{
    /**
     * @param $searchTime
     * @return mixed
     */
    public function getUseCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->whereBetween('created_at', [$searchTime['start'], $searchTime['end']])->sum('change_money');
        }
        return self::uniacid()->sum('change_money');
    }
    /**
     * @param $searchTime
     * @return mixed
     */
    public function getUsedCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->where('type', 2)->whereBetween('created_at', [$searchTime['start'], $searchTime['end']])->sum('change_money');
        }
        return self::uniacid()->where('type', 2)->sum('change_money');
    }

    /**
     * @param $searchTime
     * @return mixed
     */
    public function getWithdrawCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->where('service_type', 2)->whereBetween('created_at', [$searchTime['start'], $searchTime['end']])->sum('change_money');
        }
        return self::uniacid()->where('service_type', 2)->sum('change_money');
    }



    /**
     * @param $searchTime
     * @return mixed
     */
    public function getGivenCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->whereIn('service_type', [5,7])->whereBetween('created_at', [$searchTime['start'], $searchTime['end']])->sum('change_money');
        }
        return self::uniacid()->whereIn('service_type', [5,7])->sum('change_money');
    }

}