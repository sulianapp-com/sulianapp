<?php

namespace app\frontend\modules\coupon\services\models\TimeLimit;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 下午5:17
 */
class SinceReceive extends TimeLimit
{
    public function valid()
    {

        if ($this->dbCoupon->time_days == false) {
            return true;
        }
        if ($this->receiveDays() > $this->dbCoupon->time_days) {
            return false;
        }
        return true;
    }

    private function receiveDays()
    {
        return $this->coupon->getMemberCoupon()->get_time->diffInDays();
    }
}