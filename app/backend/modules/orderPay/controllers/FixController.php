<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/15
 * Time: 下午3:44
 */

namespace app\backend\modules\orderPay\controllers;

use app\backend\modules\orderPay\fix\DoublePaymentRepair;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\models\OrderPay;

class FixController extends BaseController
{
    /**
     * @throws \app\common\exceptions\AppException
     */
    public function refund()
    {
        /**
         * @var OrderPay $orderPay
         */
        $orderPay = OrderPay::find(request('order_pay_id'));
        if(!$orderPay){
            throw new AppException('未找到支付记录'.request('order_pay_id'));
        }
        $a = (new DoublePaymentRepair($orderPay))->handle();
        dd($a);
    }
}