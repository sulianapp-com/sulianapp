<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:40 AM
 */

namespace app\frontend\modules\order\discount;

use app\common\exceptions\AppException;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\order\PriceNode;

class OrderMinDeductionPriceNode extends PriceNode
{
    private $preOrderDeduction;
    private $order;

    public function __construct(PreOrder $order, PreOrderDeduction $preOrderDeduction, $weight)
    {
        $this->order = $order;
        $this->preOrderDeduction = $preOrderDeduction;
        parent::__construct($weight);
    }

    function getKey()
    {
        return $this->preOrderDeduction->getCode() . 'MinDeduction';
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    function getPrice()
    {

        $price = $this->order->getPriceBefore($this->getKey()) - $this->preOrderDeduction->getMinDeduction()->getMoney();

        if ($price < 0) {
            throw new AppException("订单优惠后金额{$this->order->getPriceBefore($this->getKey())}元,不满足{$this->preOrderDeduction->getName()}最低抵扣{$this->preOrderDeduction->getMinDeduction()->getMoney()}元");
        }
        return $price;
    }

}