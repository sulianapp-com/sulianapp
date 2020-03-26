<?php
/**
 * Created by PhpStorm.
 * User: CGOD
 * Date: 2019/12/18
 * Time: 16:00
 */

namespace app\frontend\modules\coupon\listeners;

use app\frontend\modules\coupon\services\CronSendService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\common\models\Order;
use app\common\facades\Setting;
use app\common\models\UniAccount;
use app\common\models\coupon\OrderGoodsCoupon;

//商品购买每月赠送优惠券
class MonthCouponSend
{
    use DispatchesJobs;

    public function handle()
    {
        set_time_limit(0);
        $uniAccount = UniAccount::get() ?: [];
        foreach ($uniAccount as $u) {
            Setting::$uniqueAccountId = \YunShop::app()->uniacid = $u->uniacid;

            $this->orderCouponSend();
        }
    }

    public function orderCouponSend()
    {
        $records = OrderGoodsCoupon::uniacid()
            ->where(['send_type'=>OrderGoodsCoupon::MONTH_TYPE,'status'=>OrderGoodsCoupon::WAIT_STATUS])
            ->whereHas('hasOneOrderGoods',function ($query){
                $query->whereHas('hasOneOrder',function ($q){
                    $q->where('status',Order::COMPLETE);
                }) ;
            })
            ->get();
        if($records->isEmpty())
        {
            return;
        }
        foreach ($records as $record)
        {
            $numReason = $record->num_reason?$record->num_reason.'||':'';
            (new CronSendService($record,$numReason,2))->sendCoupon();
        }
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Month-Coupon', '0 1 1 * * *', function() {
                $this->handle();
                return;
            });
        });
    }
}