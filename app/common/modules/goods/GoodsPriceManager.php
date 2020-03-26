<?php

namespace app\common\modules\goods;

use app\common\helpers\Serializer;
use app\common\models\Goods;
use app\common\modules\goods\dealPrice\BaseDealPrice;

class GoodsPriceManager
{
    /**
     * @var Goods $goods
     */
    private $goods;
    private $detailPrice;

    public function __construct(Goods $goods)
    {
        $this->goods = $goods;
    }

    public function getDealPrice()
    {
        if (!isset($this->detailPrice)) {
            $this->detailPrice = $this->_getDealPrice();
        }
        return $this->detailPrice;
    }

    private function _getDealPrice()
    {
        $dealPrices = collect(\app\common\modules\shop\ShopConfig::current()->get('shop-foundation.goods.dealPrice'))->map(function (array $dealPriceStrategy) {
            return call_user_func($dealPriceStrategy['class'], $this->goods);
        });
 
        $dealPrices = $dealPrices->sortBy(function (BaseDealPrice $dealPrice) {

            return $dealPrice->getWeight();
        });

        /**
         * @var BaseDealPrice $dealPrice
         */
        $dealPrice = $dealPrices->first(function (BaseDealPrice $dealPrice) {
            return $dealPrice->enable();
        });

        return $dealPrice->getDealPrice();
    }
}