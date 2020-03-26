<?php
namespace app\common\modules\goods\dealPrice;

class GoodsDealPrice extends BaseDealPrice
{

    public function getDealPrice()
    {
        return $this->goods->price;
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