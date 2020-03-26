<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午1:49
 */

namespace app\frontend\modules\finance\deduction\deductionSettings;

use app\framework\Support\Facades\Log;
use app\frontend\models\Goods;
use app\frontend\modules\deduction\DeductionSettingInterface;

class PointGoodsDeductionSetting implements DeductionSettingInterface
{
    public function getWeight()
    {
        return 10;
    }

    /**
     * @var \app\frontend\models\goods\Sale
     */
    private $setting;
// todo 抵扣设置应该分为商品抵扣和订单抵扣两类, 现在缺少订单抵扣设置 ,
    function __construct($goods)
    {
        $this->setting = $goods->hasOneSale;

    }
// todo 这个方法应该放在订单抵扣设置中
    public function isEnableDeductDispatchPrice()
    {
        Log::debug("监听订单抵扣设置",\Setting::get('point.set.point_freight'));
        return \Setting::get('point.set.point_freight');
    }

    public function isDispatchDisable()
    {
        // 商品抵扣设置为0,则商品不参与抵扣
        return !\Setting::get('point.set.point_freight') || $this->setting->max_point_deduct === '0';
    }

    public function isMaxDisable()
    {
        return !\Setting::get('point.set.point_deduct') || $this->setting->max_point_deduct === '0';
    }

    public function isMinDisable()
    {
        return !\Setting::get('point.set.point_deduct') || $this->setting->min_point_deduct === '0';
    }

    public function getMaxFixedAmount()
    {
        return str_replace('%', '', $this->setting->max_point_deduct) ?: false;
    }

    public function getMaxPriceProportion()
    {
        if (!$this->setting->max_point_deduct) {
            return false;
        }

        return str_replace('%', '', $this->setting->max_point_deduct);
    }

    public function getMaxDeductionType()
    {
        // 商品抵扣设置为空,则商品未设置独立抵扣
        if($this->setting->max_point_deduct === ''){
            return false;
        }
        if(strexists($this->setting->max_point_deduct, '%')){
            return 'GoodsPriceProportion';
        }
        return 'FixedAmount';
    }
    public function getMinFixedAmount()
    {
        return str_replace('%', '', $this->setting->min_point_deduct) ?: false;
    }

    public function getMinPriceProportion()
    {
        if (!$this->setting->min_point_deduct) {
            return false;
        }

        return str_replace('%', '', $this->setting->min_point_deduct);
    }

    public function getMinDeductionType()
    {
        // 商品抵扣设置为空,则商品未设置独立抵扣
        if($this->setting->min_point_deduct === ''){
            return false;
        }
        if(strexists($this->setting->min_point_deduct, '%')){
            return 'GoodsPriceProportion';
        }
        return 'FixedAmount';
    }
}