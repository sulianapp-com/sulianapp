<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 上午11:01
 */

namespace app\frontend\modules\deduction;

/**
 * 商品抵扣基类
 * Class GoodsDeduction
 * @package app\frontend\modules\deduction
 */
abstract class GoodsDeduction
{
    protected $deductionSettingCollection;

    function __construct(DeductionSettingCollection $deductionSettingCollection)
    {
        $this->deductionSettingCollection = $deductionSettingCollection;
    }

    abstract public function getCode();

    /**
     * @return DeductionSettingCollection
     */
    public function getDeductionSettingCollection()
    {
        return $this->deductionSettingCollection;
    }

    public function getMaxPriceProportion()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidMaxPriceProportion();
    }

    public function getMaxFixedAmount()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidMaxFixedAmount();
    }

    public function getMaxDeductionAmountCalculationType()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidMaxCalculationType();
    }

    /**
     * 获取商品最少可以抵扣的价格比例
     * @return float
     */
    public function getMinPriceProportion()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidMinPriceProportion();
    }

    /**
     * 获取商品最少可以抵扣的固定金额
     * @return float
     */
    public function getMinFixedAmount()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidMinFixedAmount();
    }

    /**
     * 获取抵扣金额最小值计算方式
     * @return string
     */
    public function getMinDeductionAmountCalculationType()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidMinCalculationType();

    }

    /**
     * 商品可使用抵扣
     * @param $goods
     * @return bool
     */
    abstract public function deductible($goods);

}