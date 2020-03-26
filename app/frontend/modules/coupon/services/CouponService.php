<?php

namespace app\frontend\modules\coupon\services;

use app\common\helpers\ArrayHelper;
use app\common\models\coupon\OrderGoodsCoupon;
use app\common\models\goods\GoodsCoupon;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\order\models\PreOrder;
use app\Jobs\addGoodsCouponQueueJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;

class CouponService
{
    use DispatchesJobs;
    /**
     * @var PreOrder
     */
    private $order;
    private $orderGoods;
    private $coupon_method;
    private $selectedMemberCoupon;

    public function __construct($order, $coupon_method = null, $orderGoods = [])
    {
        $this->order = $order;
        $this->orderGoods = $orderGoods;
        $this->coupon_method = $coupon_method;
    }

    /**
     * 获取订单优惠金额
     * @return float
     */
    public function getOrderDiscountPrice()
    {
        return $this->getAllValidCoupons()->sum(function (Coupon $coupon) {
            if (!$coupon->valid()) {
                return 0;
            }
            /**
             * @var $coupon Coupon
             */
            $coupon->activate();
            return $coupon->getDiscountAmount();
        });
    }

    /**
     * 获取订单可算的优惠券
     * @return Collection
     */
    public function getOptionalCoupons()
    {
        //用户所有优惠券
        $coupons = $this->getMemberCoupon()->map(function ($memberCoupon) {
            return new Coupon($memberCoupon, $this->order);
        });

        //其他优惠组合后可选的优惠券
        $coupons = $coupons->filter(function ($coupon) {
            /**
             * @var $coupon Coupon
             */
            //不可选
            if (!$coupon->isOptional()) {
                trace_log()->coupon("优惠券{$coupon->getMemberCoupon()->id}", '不可选');
                return false;
            }
            //商城开启了多张优惠券 并且当前优惠券组合可以继续添加这张
            $coupon->getMemberCoupon()->valid = (!\Setting::get('coupon.is_singleton') || $this->order->orderCoupons->isEmpty()) && $coupon->valid();//界面标蓝
            $coupon->getMemberCoupon()->checked = false;//界面选中

            return true;
        })->values();

        //已选的优惠券
        $coupons = collect($this->order->orderCoupons)->map(function ($orderCoupon) {
            // 已参与订单价格计算的优惠券
            $orderCoupon->coupon->getMemberCoupon()->valid = true;
            $orderCoupon->coupon->getMemberCoupon()->checked = true;
            return $orderCoupon->coupon;
        })->merge($coupons);

        //按member_coupon的id倒序
        $coupons = $coupons->sortByDesc(function ($coupon) {
            return $coupon->getMemberCoupon()->id;
        })->values();

        return $coupons;
    }

    /**
     * 获取所有选中并有效的优惠券
     * @return Collection
     */
    public function getAllValidCoupons()
    {
        $coupon = $this->getSelectedMemberCoupon()->map(function ($memberCoupon) {
            return new Coupon($memberCoupon, $this->order);
        });
        $result = $coupon->filter(function ($coupon) {
            /**
             * @var $coupon Coupon
             */
            return $coupon->valid();
        });

        return $result;
    }

    /**
     * 用户拥有的优惠券
     * @return Collection
     */
    private function getMemberCoupon()
    {
        $coupon_method = $this->coupon_method;

        $result = MemberCouponService::getCurrentMemberCouponCache($this->order->belongsToMember);

        if (isset($coupon_method)) {// 折扣/立减
            $result = $result->filter(function ($memberCoupon) use ($coupon_method) {
                return $memberCoupon->belongsToCoupon->coupon_method == $coupon_method;
            });
        }

        return $result;

    }

    /**
     * 用户拥有并选中的优惠券
     * @return Collection
     */
    private function getSelectedMemberCoupon()
    {
        if (!isset($this->selectedMemberCoupon)) {
            $member_coupon_ids = ArrayHelper::unreliableDataToArray(\Request::input('member_coupon_ids'));

            if (\Setting::get('coupon.is_singleton')) {
                $member_coupon_ids = array_slice($member_coupon_ids, 0, 1);
            }
            $this->selectedMemberCoupon = $this->getMemberCoupon()->filter(function ($memberCoupon) use ($member_coupon_ids) {
                return in_array($memberCoupon->id, $member_coupon_ids);
            });
        }

        return $this->selectedMemberCoupon;
    }

    public function sendCoupon()
    {
        $orderGoods = $this->orderGoods;
        foreach ($orderGoods as $goods) {
            $goodsCoupon = GoodsCoupon::ofGoodsId($goods->goods_id)->first();

            //dump($goodsCoupon);
            //未开启 或 已关闭 或 未设置优惠券
            if (!$goodsCoupon || !$goodsCoupon->is_give || !$goodsCoupon->coupon) {
                continue;
            }

            //每月发送时，发送月数 为空 或为 0
            if ($goodsCoupon->send_type == '0' && empty($goodsCoupon->send_num)) {
                continue;
            }

            for ($i = 1; $i <= $goods->total; $i++) {

                switch ($goodsCoupon->send_type) {
                    //订单完成立即发送
                    case '1':
                        $this->promptlySendCoupon($goodsCoupon);
                        break;
                    //每月发送
                    case '0':
                        $this->everyMonthSendCoupon($goodsCoupon);
                        break;
                }
            }
        }
    }

    /**
     * 商品消费赠送优惠券新方式
     */
    public function sendCouponLog()
    {
        $orderGoods = $this->orderGoods;
        foreach ($orderGoods as $goods) {
            $goodsCoupon = GoodsCoupon::ofGoodsId($goods->goods_id)->first();

            //dump($goodsCoupon);
            //未开启 或 已关闭 或 未设置优惠券
            if (!$goodsCoupon || !$goodsCoupon->is_give || !$goodsCoupon->coupon) {
                continue;
            }

            //每月发送时，发送月数 为空 或为 0
            if ($goodsCoupon->send_type == '0' && empty($goodsCoupon->send_num)) {
                continue;
            }


            $this->doCouponLog($goodsCoupon,$goods->id,$goods->total);
        }
    }

    public function promptlySendCoupon($goodsCoupon)
    {
        $coupon_ids = [];
        foreach ($goodsCoupon->coupon as $key => $item) {
            if ($item['coupon_several'] > 1) {
                for ($i = 1; $i <= $item['coupon_several']; $i++) {
                    $coupon_ids[] = $item['coupon_id'];
                }
            } else {
                $coupon_ids[] = $item['coupon_id'];
            }
        }

        (new CouponSendService())->sendCouponsToMember($this->order->uid, $coupon_ids, 4, $this->order->order_sn);
    }

    public function everyMonthSendCoupon($goodsCoupon)
    {
        foreach ($goodsCoupon->coupon as $key => $item) {
            if ($item['coupon_several'] > 1) {
                for ($i = 1; $i <= $item['coupon_several']; $i++) {
                    $this->addSendCouponQueue($goodsCoupon, $item['coupon_id']);
                }
            } else {
                $this->addSendCouponQueue($goodsCoupon, $item['coupon_id']);
            }
        }
    }

    /**
     * @param $goodsCoupon
     * @param $orderGoodsId
     * @param $total
     * 为了不多发少发，从源头控制锁定原因，定时发放
     */
    public function doCouponLog($goodsCoupon,$orderGoodsId,$total)
    {
        foreach ($goodsCoupon->coupon as $key => $item) {
            $insertArr = [
                'uniacid' => \YunShop::app()->uniacid,
                'order_goods_id' => $orderGoodsId,
                'coupon_id' => $item['coupon_id'],
                'coupon_several' => bcmul($item['coupon_several'],$total),
                'send_type' =>  $goodsCoupon->send_type,
                'remark' => json_encode($goodsCoupon->coupon)
            ];
            if($goodsCoupon->send_type == 0)
            {
                $monthArr = [
                    'send_num' => $goodsCoupon->send_num,
                    'end_send_num' => 0,
                ];
                $insertArr = array_merge($insertArr,$monthArr);
            }
            $model = new OrderGoodsCoupon();
            $model->fill($insertArr);
            $model->save();
        }
    }

    public function addSendCouponQueue($goodsCoupon, $coupon_id)
    {
        $queueData = [
            'uniacid' => \YunShop::app()->uniacid,
            'goods_id' => $goodsCoupon->goods_id,
            'uid' => $this->order->uid,
            'coupon_id' => $coupon_id,
            'send_num' => $goodsCoupon->send_num,
            'end_send_num' => 0,
            'status' => 0,
            'created_at' => time()
        ];
        $this->dispatch((new addGoodsCouponQueueJob($queueData)));
    }

}