<?php

namespace app\common\modules\goodsOption;

use app\common\helpers\Serializer;
use app\common\models\GoodsOption;
use app\common\modules\goodsOption\dealPrice\BaseDealPrice;

class GoodsOptionPriceManager
{
    /**
     * @var GoodsOption $goodsOption
     */
    private $goodsOption;
    private $detailPrice;

    public function __construct(GoodsOption $goodsOption)
    {
        $this->goodsOption = $goodsOption;
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
        $dealPrices = collect(\app\common\modules\shop\ShopConfig::current()->get('shop-foundation.goods-option.dealPrice'))->map(function (array $dealPriceStrategy) {

            return call_user_func($dealPriceStrategy['class'], $this->goodsOption);
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