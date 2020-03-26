<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 取消支付
 */

namespace app\frontend\modules\order\services\behavior;


use app\common\models\Order;

class OrderCancelPay extends ChangeStatusOperation
{
    protected $statusBeforeChange = [Order::WAIT_SEND];
    protected $statusAfterChanged = 0;
    protected $name = '取消支付';
    protected $time_field = 'cancel_pay_time';

    protected $past_tense_class_name = 'OrderCancelPaid';
}