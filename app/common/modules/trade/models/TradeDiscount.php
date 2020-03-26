<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/23
 * Time: 5:11 PM
 */

namespace app\common\modules\trade\models;


use app\common\models\BaseModel;


class TradeDiscount extends BaseModel
{
    /**
     * @var Trade
     */
    private $trade;

    public function init(Trade $trade)
    {
        $this->trade = $trade;
        $this->setRelation('memberCoupons', $this->getCoupons());
        return $this;
    }

    private function getCoupons()
    {
        return $this->trade->orders->getMemberCoupons();

    }

}