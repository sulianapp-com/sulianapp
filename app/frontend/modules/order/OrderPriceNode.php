<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:41 AM
 */

namespace app\frontend\modules\order;


use app\frontend\modules\order\models\PreOrder;

abstract class OrderPriceNode extends PriceNode
{
    protected $order;
    protected $weight;

    public function __construct(PreOrder $preOrder, $weight)
    {
        $this->order = $preOrder;
        parent::__construct($weight);
    }

    public function getWeight()
    {
        return $this->weight;
    }
}