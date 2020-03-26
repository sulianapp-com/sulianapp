<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/15
 * Time: 下午1:49
 */

namespace app\backend\modules\order\fix;

use app\backend\modules\orderPay\fix\DoublePaymentRepair;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\services\PayFactory;

class OrderPayFailRepair
{
    /**
     * @var Order
     */
    private $order;
    public $message = [];

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return bool
     * @throws \app\common\exceptions\AppException
     */
    public function handle()
    {

        if (!$this->check()) {
            $this->message[] = '不满足修复条件';

            return false;
        }
        /**
         * @var OrderPay $orderPay
         */
        $orderPay = $this->order->orderPays->where('status', 1)->sort('update_at')->first();
        $orderPay->pay();
        $this->message[] = $this->order->order_sn.'已修复';
        // todo 剩余的记录执行退款
        $this->order->orderPays->where('status', 1)->where('id', '!=', $orderPay->id)->each(
            function (OrderPay $orderPay) {
                $this->message[] = $orderPay->pay_sn . '已退款';
                (new DoublePaymentRepair($orderPay))->handle();
            }

        );


    }

    private function check()
    {
        // 待支付
        if ($this->order->status != Order::WAIT_PAY) {
            $this->message[] = '订单已支付';
            return false;
        }

        // 已付款
        if (count($this->order->orderPays->where('status', 1)) > 0) {
            return true;
        }
        $this->message[] = '订单没有成功付款记录';

        return false;
    }

}