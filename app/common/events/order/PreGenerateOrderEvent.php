<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/18
 * Time: 下午2:14
 */

namespace app\common\events\order;


use app\common\events\Event;
use app\frontend\modules\order\models\PreOrder;

abstract class PreGenerateOrderEvent extends Event
{
    private $preOrder;

    public function __construct(PreOrder $orderModel)
    {
        $this->preOrder = $orderModel;
    }

    /**
     * @return PreOrder
     */
    public function getOrderModel(){
        return $this->preOrder;
    }
}