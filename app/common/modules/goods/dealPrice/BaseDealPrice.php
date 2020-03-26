<?php


namespace app\common\modules\goods\dealPrice;


use app\common\models\Goods;

abstract class BaseDealPrice
{
    /**
     * @var Goods
     */
    protected $goods;

    public function __construct(Goods $goods)
    {
        $this->goods = $goods;
    }

    abstract public function getWeight();
    abstract public function enable();

    abstract public function getDealPrice();
}