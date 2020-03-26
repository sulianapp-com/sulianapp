<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/27
 * Time: 下午1:58
 */

namespace app\common\models;


/**
 * Class GoodsDiscount
 * @package app\common\models
 * @property int discount_method
 * @property int discount_value
 */
class GoodsDiscount extends BaseModel
{
    public $table = 'yz_goods_discount';
    public $guarded = [];
    const MONEY_OFF = 1;//立减
    const DISCOUNT = 2;//折扣
    public $amount;

    /**
     * 开启商品独立优惠
     * @return bool
     */
    public function enable()
    {
        //设置了折扣方式 并且 设置了折扣值
        return $this->discount_method != 0 && $this->discount_value !== '';
    }

    /**
     * @param $price
     * @return int|mixed
     * @throws \app\common\exceptions\AppException
     */
    public function getAmount($price,$member = null)
    {

        if(array_key_exists('amount',$this->attributes)){
            return $this->amount;
        }
        if ($this->enable()) {
            $this->amount =  $this->getIndependentDiscountAmount($price);
        } else {
            $this->amount =  $this->getGlobalDiscountAmount($price,$member);
        }
        return $this->amount;
    }

    /**
     * @param $price
     * @return int
     * @throws \app\common\exceptions\AppException
     */
    public function getGlobalDiscountAmount($price,$member = null)
    {
        //$member = \app\frontend\models\Member::current();
        if (!isset($member->yzMember->level)) {
            return 0;
        }
        return $member->yzMember->level->getMemberLevelGoodsDiscountAmount($price);
    }

    /**
     * 获取等级优惠金额
     * @param $price
     * @return int|mixed
     */
    public function getIndependentDiscountAmount($price)
    {
        //其次等级商品全局设置
        switch ($this->discount_method) {
            case self::DISCOUNT:
                $result = $this->getMoneyAmount();
                break;
            case self::MONEY_OFF:
                $result = $this->getDiscountAmount($price);
                break;
            default:
                $result = $price;
                break;
        }
        return $result ? $result : 0;
    }

    /**
     * 商品独立等级立减后优惠金额
     * @return mixed
     */
    private function getMoneyAmount()
    {
        if ($this->discount_value == 0) {
            return 0;
        }
        return $this->discount_value;
    }

    /**
     * 商品独立等级折扣优惠金额
     * @param $price
     * @return mixed
     */
    private function getDiscountAmount($price)
    {

        if ($this->discount_value == 0) {

            return 0;
        }
        return $price * (1 - $this->discount_value / 10);
    }

    public function goods()
    {
        $this->belongsTo(Goods::class);
    }
}