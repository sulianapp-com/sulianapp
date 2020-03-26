<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/15
 * Time: 下午3:53
 */

namespace app\backend\modules\orderPay\fix;

use app\common\exceptions\AppException;
use app\common\models\OrderPay;
use app\common\services\PayFactory;

class DoublePaymentRepair
{
    public $message=[];
    /**
     * @var OrderPay
     */
    private $orderPay;

    /**
     * DoublePaymentRepair constructor.
     * @param OrderPay $orderPay
     */
    public function __construct(OrderPay $orderPay)
    {
        $this->orderPay = $orderPay;
    }

    /**
     * @throws AppException
     */
    public function handle()
    {
        $this->orderPay->fastRefund();

        $this->message[]="{$this->orderPay->pay_type_id}退款成功";
        return $this->message;
    }

    /**
     * @throws AppException
     */
    public function check()
    {
        if ($this->orderPay != OrderPay::STATUS_PAID){
            throw new AppException($this->orderPay->status_name.'的支付单无法退款');
        }

        //todo 对应的订单已经支付

        //todo 对应的订单有其他有效的支付记录

    }
}