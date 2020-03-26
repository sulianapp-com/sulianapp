<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 上午11:01
 */

namespace app\frontend\modules\deduction;

/**
 * 抵扣设置
 * Interface DeductionSetting
 * @package app\frontend\modules\deduction
 */
interface DeductionSettingInterface
{
    /**
     * @return int
     */
    public function getWeight();

    /**
     * @return bool
     */
    public function isEnableDeductDispatchPrice();

    /**
     * @return bool 已禁用
     */
    public function isMaxDisable();
    public function isMinDisable();
    public function isDispatchDisable();

    public function getMaxFixedAmount();
    public function getMaxPriceProportion();
    /**
     * 根据这个方法判断实例化哪个金额类
     * @return mixed
     */
    public function getMaxDeductionType();

    public function getMinFixedAmount();
    public function getMinPriceProportion();
    /**
     * 根据这个方法判断实例化哪个金额类
     * @return mixed
     */
    public function getMinDeductionType();
}