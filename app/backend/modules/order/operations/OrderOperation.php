<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/3/26
 * Time: 2:00 PM
 */
namespace app\backend\modules\order\operations;

use app\common\models\Order;
use app\frontend\modules\order\operations\OrderOperationInterface;

abstract class OrderOperation implements OrderOperationInterface
{
    const ADMIN_PAY = 1;
    const ADMIN_SEND = 2;
    const ADMIN_RECEIVE = 3;
    const ADMIN_CLOSE = -1;

    /**
     * @var Order
     */
    protected $order;

    /**
     * AdminProviderOrderOperation constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}