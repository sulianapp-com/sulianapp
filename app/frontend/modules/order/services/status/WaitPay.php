<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\Order;
use app\common\models\PayType;

class WaitPay extends Status
{
    /**
     * @var Order
     */
    private $order;
    protected $name = '付款';
    protected $value;
    protected $api = 'order.operation.pay';

    public function __construct(Order $order)
    {
        $this->value = static::PAY;

        $this->order = $order;
    }

    public function getStatusName()
    {
        return '待付款';
    }
}