<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 13:53
 */

namespace app\frontend\modules\coupon\listeners;

use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\models\coupon\ShoppingShareCoupon;
use app\common\models\goods\GoodsCoupon;
use app\common\models\Member;

class ShoppingShareCouponListener
{

    private $order;

    private $set;

    private $share_member;

    /**
     * @param $events
     * 监听事件
     */
    public function subscribe($events)
    {
        $events->listen(AfterOrderPaidImmediatelyEvent::class, self::class . '@onShareCouponCreated');

    }

    public function onShareCouponCreated(AfterOrderPaidImmediatelyEvent $event)
    {

        $this->order = $event->getOrderModel();

        $this->set = \Setting::get('coupon.shopping_share');

        $this->share_member = Member::with(['yzMember'])->find($this->order->uid);


        //拥有推广资格的会员才可以分享
        if ($this->set['share_limit'] == 1 && $this->share_member->yzMember->is_agent != 1) {
           return;
        }

        if ($this->order->hasManyOrderGoods->isEmpty()) {
            return;
        }

        $goods = $this->order->hasManyOrderGoods->map(function ($orderGoods) {
            return ['goods_id'=> $orderGoods->goods_id, 'total'=> $orderGoods->total];
        })->toArray();

        $goods_ids = array_map(function ($id) {
            return $id['goods_id'];
        },$goods);

        $goods_coupons = GoodsCoupon::whereIn('goods_id', $goods_ids)->where('shopping_share', 1)->get();

        if ($goods_coupons->isEmpty()) {
            return;
        }

        $coupons = $goods_coupons->map(function($goods_coupon) {
            return ['goods_id'=> $goods_coupon->goods_id, 'share_coupon'=> $goods_coupon->share_coupon];
        })->toArray();


        //todo 购买单个商品的数量就有多少次分享次数
        $share_coupon = [];
        foreach ($goods as $item) {

            foreach ($coupons as $value) {
                if ($item['goods_id'] == $value['goods_id']) {
                    for ($i = 0; $i < $item['total']; $i++) {
                        $share_coupon =  array_merge($share_coupon, $value['share_coupon']);
                    }
                }
            }
        }

        $this->addData($share_coupon);

    }

    /**
     * 保存分享优惠卷数据
     * @param $coupons
     */
    protected function addData($coupons)
    {

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $this->order->uid,
            'order_id'  => $this->order->id,
            'obtain_restriction' => $this->set['receive_limit']?1:0,
            'share_coupon' => $this->dealWith($coupons),
        ];


        ShoppingShareCoupon::create($data);
    }

    /**
     * 只保存分享优惠卷的id 集合
     * @param $coupons
     * @return array
     */
    protected function dealWith($coupons)
    {
        $coupon_ids = [];
        foreach ($coupons as $coupon) {
            for ($i = 0; $i < $coupon['coupon_several']; $i++) {
                $coupon_ids[] = $coupon['coupon_id'];
            }
        }
        return $coupon_ids;
    }
}