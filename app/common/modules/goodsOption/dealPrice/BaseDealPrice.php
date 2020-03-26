<?php


namespace app\common\modules\goodsOption\dealPrice;



use app\common\models\GoodsOption;

abstract class BaseDealPrice
{
    /**
     * @var GoodsOption
     */
    protected $goodsOption;

    public function __construct(GoodsOption $goodsOption)
    {
        $this->goodsOption = $goodsOption;
    }

    abstract public function getWeight();
    abstract public function enable();

    abstract public function getDealPrice();
}