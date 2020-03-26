<?php

namespace app\backend\modules\coupon\listeners;

use app\backend\modules\coupon\services\MessageNotice;
use app\backend\modules\coupon\models\OrderCouponReturn;
use app\common\models\CouponLog;
/**
 * 未付款订单,退款订单关闭后，返还优惠券
 * 这里不用区分是不是未付款或退款才关闭的订单
 * 只要订单关闭，就返还优惠券
 * 返还：修改ye_member_coupon的used为0，记录yz_coupon_log表，记录yz_order_coupon_return表，发送消息通知
*/
class OrderClosedListener
{
	
	public function subscribe($event)
	{
	    // 订单取消事件，就是订单关闭事件
        $event->listen(\app\common\events\order\AfterOrderCanceledEvent::class, OrderClosedListener::class. '@returnCoupon');
	}

	public function returnCoupon(\app\common\events\order\AfterOrderCanceledEvent $event)
	{
        //获取设置的值
        $isReturn = \app\common\facades\Setting::get('coupon.order_close_return');
        if ($isReturn) {//如果开启订单返还优惠券，则进行返还操作
            $order = \app\common\models\Order::find($event->getOrderModel()->id);
            // 根据订单id查询order_coupon表，得到member_coupon的id
            $orderCoupons = \app\common\models\order\OrderCoupon::where('order_id',$order->id)->get();
            foreach ($orderCoupons as $orderCoupon) {
                $memberCoupon = \app\common\models\MemberCoupon::find($orderCoupon->member_coupon_id);
                if (!empty($memberCoupon) && $memberCoupon->used == 1) {
                    \Log::info('-----订单关闭优惠券返还------订单id:'.$orderCoupon->order_id.' 优惠券id:'.$orderCoupon->coupon_id.' 会员id:'.$memberCoupon->uid);
                    // 修改member_coupon的使用状态
                    $this->changeMemberCouponUsedStatus($memberCoupon,0,0);
                    // 记录日志表
                    $this->logCoupon($memberCoupon,$order);
                    // 做新表记录返还的order_coupon_id
                    $this->logOrderCouponReturn($orderCoupon);
                    // 发送消息
                    MessageNotice::couponNotice($memberCoupon->coupon_id, $memberCoupon->uid);
                    // 是否需要做新的返还消息通知
                    //MessageNotice::orderCouponReturnNotice($memberCoupon->coupon_id, $memberCoupon->uid, $orderCoupon->order_id);
                }
            }
        }
	}

    /**
     * @param $memberCoupon \app\common\models\MemberCoupon
     * @param $used int
     * @param $use_time int
     */
	protected function changeMemberCouponUsedStatus($memberCoupon, $used, $use_time)
    {
        $memberCoupon->used = $used;
        $memberCoupon->use_time = $use_time;
        $memberCoupon->save();
    }

    /**
     * @param $memberCoupon \app\common\models\MemberCoupon
     * @param $order \app\common\models\Order
     */
    protected function logCoupon($memberCoupon, $order)
    {
        $log = '优惠券返还成功,订单:' . $order->order_sn . ' 成功返还 ' . 1 . ' 张优惠券:( ID为 ' . $memberCoupon->coupon_id . ' )给用户( Member ID 为 ' . $memberCoupon->uid . ' )';
        $logData = [
            'uniacid' => \YunShop::app()->uniacid,
            'logno' => $log,
            'member_id' => $memberCoupon->uid,
            'couponid' => $memberCoupon->coupon_id,
            'paystatus' => 0,
            'creditstatus' => 0,
            'paytype' => 0,
            'getfrom' => 0,
            'status' => 0,
            'createtime' => time(),
        ];
        CouponLog::create($logData);
    }

    /**
     * @param $orderCoupon \app\common\models\order\OrderCoupon
     */
    protected function logOrderCouponReturn($orderCoupon)
    {
        $orderCouponReturnData = [
            'uniacid' => \YunShop::app()->uniacid,
            'order_coupon_id' => $orderCoupon->id,
            'return_time' => time(),
        ];
        OrderCouponReturn::create($orderCouponReturnData);
    }
}