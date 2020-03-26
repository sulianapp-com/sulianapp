<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/15
 * Time: 下午3:53
 */

namespace app\backend\modules\payOrder\fix;

use app\common\events\payment\ChargeComplatedEvent;
use app\common\exceptions\AppException;
use app\common\models\Order;
use app\common\models\PayOrder;
use app\common\models\PayType;
use app\frontend\modules\order\services\OrderService;

class PaymentCallbackRepair
{
    public $message=[];
    /**
     * @var PayOrder
     */
    private $payOrder;

    /**
     * PaymentCallbackRepair constructor.
     * @param PayOrder $payOrder
     */
    public function __construct(PayOrder $payOrder)
    {
        $this->payOrder = $payOrder;
    }

    /**
     * @throws AppException
     */
    public function handle()
    {
        $this->message[]="{$this->payOrder->orderPay->pay_sn}开始修复";

        if(!$this->check()){
            return false;
        }
        OrderService::ordersPay(['order_pay_id' => $this->payOrder->orderPay->id,'pay_type_id' => PayType::PAY_CLOUD_WEACHAT]);

        event(new ChargeComplatedEvent([
            'order_sn' => $this->payOrder->orderPay->pay_sn,
            'pay_sn' => $this->payOrder->trade_no,
            'order_pay_id' => $this->payOrder->orderPay->id
        ]));
        $this->message[]="{$this->payOrder->orderPay->pay_sn}已修复";
        return $this->message;
    }

    /**
     * @return bool
     */
    public function check()
    {
        if($this->payOrder->status != 2){
            $this->message[] = "平台支付记录状态为{$this->payOrder->status_name}";
            return false;
        }
        foreach ($this->payOrder->orderPay->orders as $order){
            /**
             * @var Order $order
             */
            if($order->status != 0){
                $this->message[] = "订单状态为{$order->statusName}";

                return false;
            }
        }

        return true;
    }
}