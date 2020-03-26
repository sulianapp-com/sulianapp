<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/29
 * Time: 11:40 AM
 */

namespace app\common\modules\discount;

use app\common\models\Goods;
use app\frontend\models\Member;

/**
 * 商品会员等级优惠计算类
 * Class GoodsMemberLevelDiscount
 * @package app\common\modules\discount
 */
class GoodsMemberLevelDiscount
{
    private $goods;
    private $member;

    public function __construct(Goods $goods, Member $member)
    {
        $this->goods = $goods;
        $this->member = $member;
    }

    /**
     * 计算者
     * @return BaseGoodsMemberLevelDiscountCalculator
     */
    private function getDiscountCalculator($price)
    {
        // 从配置文件中载入,按优先级排序 遍历取到第一个通过验证的 计算者
        $calculatorConfigs = collect(\app\common\modules\shop\ShopConfig::current()->get('shop-foundation.discount.GoodsMemberLevelDiscountCalculator'))->sortBy('priority');
        // 返回第一个通过验证的计算者
        foreach ($calculatorConfigs as $calculatorConfig) {
            /**
             * @var BaseGoodsMemberLevelDiscountCalculator $calculator
             */
            $calculator = call_user_func($calculatorConfig['class'], $this->goods, $this->member);
            if ($calculator->validate($price)) {
                // 通过验证返回
                return $calculator;
            }
        }
        // 默认计算者
        return new NoneGoodsMemberLevelDiscountCalculator($this->goods, $this->member);
    }

    /**
     * @param $price
     * @return float
     */
    public function getAmount($price)
    {
        return $this->getDiscountCalculator($price)->getAmount($price);
    }

    public function getLog($amount)
    {
        return $this->getDiscountCalculator($amount)->getLog($amount);
    }
}