<?php
namespace app\frontend\modules\coupon\listeners;

use app\common\facades\Setting;
use app\common\models\Coupon;
use app\common\models\GoodsCouponQueue;
use app\common\models\UniAccount;
use app\Jobs\addSendCouponJob;
use app\Jobs\addSendCouponLogJob;
use app\Jobs\updateCouponQueueJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\backend\modules\coupon\services\MessageNotice;
use app\common\models\MemberCoupon;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/12
 * Time: 下午4:28
 */
class CouponSend
{
    use DispatchesJobs;
    public $set;
    public $setLog;
    public $uniacid;

    public function handle()
    {
        \Log::info('发放优惠券处理');
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->uniacid = $u->uniacid;
            $this->setLog = Setting::get('shop.coupon_send_log');
            $this->sendCoupon();
        }
    }

    public function sendCoupon()
    {
        if (date('H') != '0') {
            \Log::info('------'.\YunShop::app()->uniacid.'商品优惠券不满足1----'.date('H').'---');
            return;
        }
        if (date('d') != '1') {
            \Log::info('------'.\YunShop::app()->uniacid.'商品优惠券不满足2----'.date('d').'---');
            return;
        }
        if (date('m') == $this->setLog['current_m']) {
            \Log::info('------'.\YunShop::app()->uniacid.'商品优惠券不满足3----'.date('m').'---');
            return;
        }
        $this->setLog['current_m'] = date('m');
        Setting::set('shop.coupon_send_log', $this->setLog);
        $couponSendQueues = GoodsCouponQueue::getCouponQueue()->get();
        // 统计优惠券已发放数量
        $surplus = [];
        $surplusNums = [];//用于统计 剩余未发放数量
        foreach ($couponSendQueues as $couponSendQueue) {
            $updatedData = [];
            $coupon = $couponSendQueue->hasOneCoupon;
            //$surplusNums['coupon_id_' . $coupon->id] = isset($surplusNums['coupon_id_' . $coupon->id]) ? $surplusNums['coupon_id_' . $coupon->id] : $coupon->surplus;
            if ($coupon->total != -1) {//限制发放数量
                // 已领取
                $count = MemberCoupon::uniacid()->where("coupon_id", $coupon->id)->count();
                // 剩余
                $surplus['coupon_id_' . $coupon->id] = $coupon->total - $count;
            } else {
                $surplus['coupon_id_' . $coupon->id] = 1;
            }
            $surplusNums['coupon_id_' . $coupon->id] = isset($surplusNums['coupon_id_' . $coupon->id]) ? $surplusNums['coupon_id_' . $coupon->id] : $surplus['coupon_id_' . $coupon->id];

            if ($surplusNums['coupon_id_' . $coupon->id] <= 0) {
                continue;
            }
            $this->sendCouponForMember($couponSendQueue);//发放优惠券到会员
            //发送获取通知
            MessageNotice::couponNotice($couponSendQueue->coupon_id,$couponSendQueue->uid);
            $this->sendCouponLog($couponSendQueue);//发放优惠券LOG

            $condition = [
                'id' => $couponSendQueue->id
            ];
            $updatedData['end_send_num'] = $couponSendQueue->end_send_num + 1;
            if ($updatedData['end_send_num'] == $couponSendQueue->send_num) {
                $updatedData['status'] = 1;
            }
            $this->dispatch((new updateCouponQueueJob($condition, $updatedData)));

            if ($coupon->total != -1) {
                $surplusNums['coupon_id_' . $coupon->id] -= 1;
            }
        }
        \Log::info('------商品优惠券每月自动发放结束-------');
    }

    public function sendCouponForMember($couponSendQueue)
    {
        $data = [
            'uniacid' => $couponSendQueue->uniacid,
            'uid' => $couponSendQueue->uid,
            'coupon_id' => $couponSendQueue->coupon_id,
            'get_type' => 0,
            'used' => 0,
            'get_time' => strtotime('now'),
        ];
        $this->dispatch((new addSendCouponJob($data)));

    }

    public function sendCouponLog($couponSendQueue)
    {
        $log = '购买商品发放优惠券成功: 商品( ID 为 ' . $couponSendQueue->goods_id . ' )成功发放 1 张优惠券( ID为 ' . $couponSendQueue->coupon_id . ' )给用户( Member ID 为 ' . $couponSendQueue->uid . ' )';
        $logData = [
            'uniacid' => $couponSendQueue->uniacid,
            'logno' => $log,
            'member_id' => $couponSendQueue->uid,
            'couponid' => $couponSendQueue->coupon_id,
            'paystatus' => 0, //todo 购买商品发放的不需要支付?
            'creditstatus' => 0, //todo 购买商品发放的不需要支付?
            'paytype' => 0, //todo 这个字段什么含义?
            'getfrom' => 0,
            'status' => 0,
            'createtime' => time(),
        ];
        $this->dispatch((new addSendCouponLogJob($logData)));

    }


    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Coupon-send', '*/10 * * * * *', function () {
                $this->handle();
                return;
            });
        });
    }
}