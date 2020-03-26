<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\DispatchType;
use app\common\models\Order;

class WaitReceive extends Status
{
    protected $name = '收货';
    protected $api = 'order.operation.receive';
    protected $value;
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->value = static::COMPLETE;
    }

    public function getStatusName()
    {
        return "待{$this->name}";
    }
}