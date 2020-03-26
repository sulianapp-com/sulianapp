<?php

namespace app\common\modules\goodsOption\dealPrice;


use app\common\facades\Setting as SettingFacades;

class MarketDealPrice extends BaseDealPrice
{
    public function getDealPrice()
    {
        return $this->goodsOption->market_price;
    }

    /**
     * @return bool
     * @throws \app\common\exceptions\MemberNotLoginException
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
        if (!$this->goodsOption->memberLevelDiscount()->getAmount($this->goodsOption->market_price)) {
            return false;
        }
        return true;
    }


    public function getWeight()
    {
        return 100;
    }

}