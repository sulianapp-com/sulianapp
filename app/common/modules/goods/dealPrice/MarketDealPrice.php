<?php

namespace app\common\modules\goods\dealPrice;


use app\common\facades\Setting as SettingFacades;

class MarketDealPrice extends BaseDealPrice
{
    public function getDealPrice()
    {
        return $this->goods->market_price;
    }

    /**
     * @return bool
     * @throws \app\common\exceptions\AppException
     */
    public function enable()
    {
        $level_discount_set = SettingFacades::get('discount.all_set');
        if (!isset($level_discount_set['type'])) {
            return false;
        }
        if ($level_discount_set['type'] != 1) {
            return false;
        }
        if (!$this->goods->memberLevelDiscount()->getAmount($this->goods->market_price)) {
            return false;
        }
        return true;
    }


    public function getWeight()
    {
        return 100;
    }

}