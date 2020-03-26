<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/29
 * Time: 2:43 PM
 */

namespace app\common\modules\discount;


class ShopGoodsMemberLevelDiscountCalculator extends BaseGoodsMemberLevelDiscountCalculator
{
    public function getAmount($price)
    {
        return $this->member->yzMember->level->getMemberLevelGoodsDiscountAmount($price);
    }
    public function validate($price)
    {
        if (!isset($this->member->yzMember->level)) {
            return false;
        }
        return true;
    }
    public function getKey()
    {
        return 'shopGoodsMemberLevel';
    }
    public function getName()
    {
        return '全场会员等级优惠';
    }
}