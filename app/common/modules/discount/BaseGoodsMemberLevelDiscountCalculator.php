<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/29
 * Time: 2:34 PM
 */

namespace app\common\modules\discount;

use app\common\models\Goods;
use app\common\models\Member;

abstract class BaseGoodsMemberLevelDiscountCalculator
{
    /**
     * @var Goods
     */
    protected $goods;
    protected $member;

    public function __construct(Goods $goods, Member $member)
    {
        $this->goods = $goods;
        $this->member = $member;
    }

    /**
     * @param $price
     * @return float
     */
    abstract public function getAmount($price);

    /**
     * @return boolean
     */
    abstract public function validate($price);

    abstract public function getKey();
    abstract public function getName();

    public function getLog($amount)
    {
        return new GoodsMemberDiscountLog([
            'code' => $this->getKey(),
            'name' => $this->getName(),
            'amount' => $amount,
        ]);
    }
}