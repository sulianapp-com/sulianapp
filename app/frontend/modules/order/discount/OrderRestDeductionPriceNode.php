<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:40 AM
 */

namespace app\frontend\modules\order\discount;

use app\frontend\models\order\PreOrderDeduction;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\order\PriceNode;

class OrderRestDeductionPriceNode extends PriceNode
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
        return $this->preOrderDeduction->getCode() . 'RestDeduction';
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    function getPrice()
    {
        if (!$this->preOrderDeduction->isChecked()) {
            return $this->order->getPriceBefore($this->getKey());
        } else {
            return $this->order->getPriceBefore($this->getKey()) - $this->preOrderDeduction->getUsablePoint()->getMoney();
        }
    }

}