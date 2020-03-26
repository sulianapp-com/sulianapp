<?php
namespace app\common\modules\goodsOption\dealPrice;

class GoodsDealPrice extends BaseDealPrice
{

    public function getDealPrice()
    {
        return $this->goodsOption->product_price;
    }

    /**
     * @return bool
     */
    public function enable()
    {
        return true;
    }

    public function getWeight()
    {
        return 1000;
    }

}