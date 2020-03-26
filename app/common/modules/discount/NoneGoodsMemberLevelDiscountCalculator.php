<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/29
 * Time: 2:43 PM
 */

namespace app\common\modules\discount;

/**
 * 没有符合的计算者
 * Class NoneGoodsMemberLevelDiscountCalculator
 * @package app\common\modules\discount
 */
class NoneGoodsMemberLevelDiscountCalculator extends BaseGoodsMemberLevelDiscountCalculator
{
    public function getAmount($price)
    {
        return 0;
    }
    public function validate($price)
    {
        return true;
    }
    public function getKey()
    {
        return 'none';
    }

    public function getName()
    {
       return '';
    }

}