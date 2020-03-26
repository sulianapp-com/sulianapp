<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/16
 * Time: 下午1:49
 */

namespace app\frontend\modules\finance\deduction;

use app\common\models\Goods;
use app\frontend\modules\deduction\DeductionSettingInterface;
use app\frontend\modules\deduction\DeductionSettingManagerInterface;
use app\frontend\modules\finance\deduction\deductionSettings\PointGoodsDeductionSetting;
use app\frontend\modules\finance\deduction\deductionSettings\PointShopDeductionSetting;
use Illuminate\Container\Container;

class PointDeductionSettingManager extends Container implements DeductionSettingManagerInterface
{
    public function __construct()
    {
        /**
         * 计积分抵扣商品设置
         */
        $this->bind('goods', function (PointDeductionSettingManager $deductionSettingManager, array $params) {
            //dump(debug_backtrace(0,20));
            return new PointGoodsDeductionSetting($params[0]);
        });
        /**
         * 积分抵扣商城设置
         */
        $this->bind('shop', function (PointDeductionSettingManager $deductionSettingManager, array $params) {
            return new PointShopDeductionSetting();
        });
    }

    /**
     * @param Goods $goods
     * @return PointDeductionSettingCollection
     */
    public function getDeductionSettingCollection(Goods $goods)
    {
        $deductionSettingCollection = collect();
        foreach ($this->getBindings() as $key => $value) {
            $deductionSettingCollection->push($this->make($key, [$goods]));
        }
        // 按权重排序
        $deductionSettingCollection = $deductionSettingCollection->sortBy(function (DeductionSettingInterface $deductionSetting) {
            return $deductionSetting->getWeight();
        });

        return new PointDeductionSettingCollection($deductionSettingCollection);
    }
}