<?php

namespace app\frontend\modules\order\coinExchange;


use app\frontend\modules\order\models\PreOrder;

class OrderCoinExchangeManager
{
    /**
     * @var OrderCoinExchangeCollection
     */
    private $orderCoinExchangeCollection;
    /**
     * @var PreOrder
     */
    private $order;

    public function __construct(PreOrder $preOrder)
    {
        $this->order = $preOrder;
    }

    public function getOrderCoinExchangeCollection()
    {
        if (!isset($this->orderCoinExchangeCollection)) {
            $this->orderCoinExchangeCollection = new OrderCoinExchangeCollection();
            $this->order->setRelation('orderCoinExchanges', $this->orderCoinExchangeCollection);

        }

        return $this->orderCoinExchangeCollection;
    }
}