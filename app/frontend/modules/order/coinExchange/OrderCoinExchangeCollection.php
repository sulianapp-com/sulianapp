<?php

namespace app\frontend\modules\order\coinExchange;


use app\framework\Database\Eloquent\Collection;
use app\frontend\models\order\PreOrderCoinExchange;

class OrderCoinExchangeCollection extends Collection
{
    public function addAndGroupByCode(PreOrderCoinExchange $orderCoinExchange)
    {
        if(!$this->sumByCode($orderCoinExchange)){
            // 不存在新建
            $this->add($orderCoinExchange);
        }
        return $this;
    }

    private function sumByCode(PreOrderCoinExchange $orderCoinExchange)
    {
        // 存在相同类型累加金额和数量
        foreach ($this->items as &$item) {
            if ($item['code'] == $orderCoinExchange->code) {
                $item['amount'] += $orderCoinExchange->amount;
                $item['coin'] += $orderCoinExchange->coin;
                return true;
            }
        }
        return false;
    }
}