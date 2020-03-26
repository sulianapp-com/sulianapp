<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午3:41
 */

namespace app\frontend\modules\orderPay\models;

use app\common\exceptions\AppException;
use app\common\models\PayType;
use app\frontend\models\Member;
use app\common\models\Order;
use app\frontend\models\OrderPay;
use app\frontend\modules\order\OrderCollection;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Database\Eloquent\Collection;

class PreOrderPay extends OrderPay
{
    /**
     * @param OrderCollection $orders
     * @throws AppException
     */
    public function setOrders(OrderCollection $orders)
    {
        $this->order_ids = $orders->pluck('id');
        $this->amount = $orders->sum('price');
        $this->uid = $orders->first()->uid;
        $this->pay_sn = OrderService::createPaySN();


        $this->validateOrders($orders);
    }

    /**
     * @param Collection $orders
     * @throws AppException
     */
    private function validateOrders(Collection $orders)
    {
        $orders->each(function (Order $order) {
            if ($order->status > Order::WAIT_PAY) {
                throw new AppException('(ID:' . $order->id . ')订单已付款,请勿重复付款');
            }
            if ($order->status == Order::CLOSE) {
                throw new AppException('(ID:' . $order->id . ')订单已关闭,无法付款');
            }

            //找人代付
            if ($order->uid != \YunShop::app()->getMemberId() && !Member::getPid() && $this->pay_type_id =! PayType::BACKEND) {
                throw new AppException('(ID:' . $order->id . ')该订单属于其他用户');
            }
            // 转账付款审核中
            if ($order->pay_type_id == PayType::REMITTANCE) {
                throw new AppException('(ID:' . $order->id . ')该订单处于转账审核中,请先关闭转账审核申请,再选择其他支付方式');
            }
            // 校验订单商品库存
            $order->stockEnough();
        });
        // 订单金额验证
        if ($orders->sum('price') < 0) {
            throw new AppException('(' . $this->orders->sum('price') . ')订单金额有误');
        }
    }

    /**
     * @throws AppException
     */
    public function store()
    {
        $this->save();
        if ($this->id === null) {
            throw new AppException('支付流水记录保存失败');

        }
        $this->orders()->attach($this->order_ids);
    }
}