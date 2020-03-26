<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/29
 * Time: 2:43 PM
 */

namespace app\common\modules\discount;


use app\common\models\GoodsDiscount;

class GoodsMemberLevelDiscountCalculator extends BaseGoodsMemberLevelDiscountCalculator
{
    /**
     * @var GoodsDiscount
     */
    private $goodsDiscount;

    /**
     * @param $price
     * @return float|int|mixed
     * @throws \app\common\exceptions\AppException
     */
    public function getAmount($price)
    {
        return $this->getGoodsDiscount()->getAmount($price,$this->member);
    }

    public function validate($price)
    {
        if (!$this->getGoodsDiscount() || $this->getGoodsDiscount()->discount_value === '') {
            return false;
        }
        return true;
    }

    public function getGoodsDiscount()
    {
        if (!isset($this->goodsDiscount)) {
            $this->goodsDiscount = $this->_getGoodsDiscount();
        }
        return $this->goodsDiscount;
    }

    private function _getGoodsDiscount()
    {
        return $this->goods->hasManyGoodsDiscount->where('level_id', $this->member->yzMember->level_id)->first();
    }
    public function getKey()
    {
        return 'independentGoodsMemberLevel';
    }
    public function getName()
    {
        return '商品会员等级优惠';
    }
}