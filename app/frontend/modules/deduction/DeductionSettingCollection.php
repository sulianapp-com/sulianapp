<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/15
 * Time: 下午9:03
 */

namespace app\frontend\modules\deduction;

use Illuminate\Database\Eloquent\Collection;

/**
 * 抵扣设置集合
 * Class DeductionSettingCollection
 * @package app\frontend\modules\deduction
 */
abstract class DeductionSettingCollection extends Collection
{
    /**
     * @return float
     */
    public function getImportantAndValidMaxFixedAmount()
    {
        // 获取抵扣设置集合中设置了抵扣金额的,权重最高的设置项
        /**
         * @var DeductionSettingInterface $deductionSetting
         */
        $priceProportion = 0;
        foreach ($this as $deductionSetting){

            if($deductionSetting->isMaxDisable()){
                $priceProportion = 0;
                break;
            }
            if($deductionSetting->getMaxFixedAmount() !== false){
                $priceProportion = $deductionSetting->getMaxFixedAmount();
                break;
            }
        }

        return $priceProportion;
    }

    /**
     * @return float
     */
    public function getImportantAndValidMaxPriceProportion()
    {

        // 找到抵扣设置集合中设置了价格比例的,权重最高的设置项

        $priceProportion = 0;
        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isMaxDisable()){
                $priceProportion = 0;
                break;
            }
            if($deductionSetting->getMaxPriceProportion() !== false){
                $priceProportion = $deductionSetting->getMaxPriceProportion();
                break;
            }
        }

        return $priceProportion;
    }

    public function getImportantAndValidMaxCalculationType(){

        $type = '';
        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isMaxDisable()){
                trace_log()->deduction("订单抵扣", "最大抵扣类型设置".get_class($deductionSetting)."禁用");
                break;
            }
            if($deductionSetting->getMaxDeductionType() !== false){
                trace_log()->deduction("订单抵扣", "最大抵扣类型设置".get_class($deductionSetting)."启用");
                $type = $deductionSetting->getMaxDeductionType();
                break;
            }
        }
        return $type;
    }

    /**
     * @return float
     */
    public function getImportantAndValidMinFixedAmount()
    {
        // 获取抵扣设置集合中设置了抵扣金额的,权重最高的设置项
        /**
         * @var DeductionSettingInterface $deductionSetting
         */
        $priceProportion = 0;

        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isMinDisable()){
                $priceProportion = 0;
                break;
            }
            if($deductionSetting->getMinFixedAmount() !== false){
                $priceProportion = $deductionSetting->getMinFixedAmount();
                break;
            }
        }
        return $priceProportion;
    }

    /**
     * @return float
     */
    public function getImportantAndValidMinPriceProportion()
    {
        // 找到抵扣设置集合中设置了价格比例的,权重最高的设置项

        $priceProportion = 0;
        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isMinDisable()){
                $priceProportion = 0;
                break;
            }
            if($deductionSetting->getMinPriceProportion() !== false){
                $priceProportion = $deductionSetting->getMinPriceProportion();
                break;
            }
        }

        return $priceProportion;
    }

    public function getImportantAndValidMinCalculationType(){

        $type = '';
        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isMinDisable()){
                break;
            }
            if($deductionSetting->getMinDeductionType() !== false){
                $type = $deductionSetting->getMinDeductionType();
                trace_log()->deduction("订单抵扣",'抵扣设置使用'.get_class($deductionSetting));
                break;
            }
        }
        return $type;
    }

    /**
     * todo 运费抵扣应该单独提取出去
     * @return bool|string
     */
    public function isEnableDeductDispatchPrice(){

        $type = '';
        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isDispatchDisable()){
                break;
            }
            if($deductionSetting->isEnableDeductDispatchPrice() !== false){
                $type = $deductionSetting->isEnableDeductDispatchPrice();
                break;
            }
        }
        return $type;
    }
}