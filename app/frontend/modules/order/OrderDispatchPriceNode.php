<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 11:36 AM
 */

namespace app\frontend\modules\order;


class OrderDispatchPriceNode extends OrderPriceNode
{

    /**
     * @return string
     */
    public function getKey()
    {
        return 'orderDispatchPrice';
    }

    /**
     * @return int|mixed|number
     * @throws \app\common\exceptions\AppException
     */
    public function getPrice()
    {
        return max($this->order->getPriceBefore($this->getKey()) + $this->order->getDispatchAmount(),0);
    }
}