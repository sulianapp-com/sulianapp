<?php
namespace app\frontend\modules\coupon\services\models\TimeLimit;

use app\common\exceptions\AppException;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 下午5:14
 */
class DateTimeRange extends TimeLimit
{
    public function valid()
    {
        if(!isset($this->dbCoupon->time_start) || !isset($this->dbCoupon->time_end)){
            throw new AppException('(ID:'.$this->dbCoupon->id.')非法优惠券数据,请联系客服');
        }
        if($this->dbCoupon->time_start->greaterThan(Carbon::now())){
            //未开始
            trace_log()->coupon("优惠券{$this->dbCoupon->id}",'未开始:'.$this->dbCoupon->time_start);

            return false;
        }

        if($this->dbCoupon->time_end->lessThan(Carbon::now())){
            //已结束
            trace_log()->coupon("优惠券{$this->dbCoupon->id}",'已结束:'.$this->dbCoupon->time_end);

            return false;
        }
        return true;
    }
}