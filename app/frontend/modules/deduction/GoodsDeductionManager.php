<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 上午10:41
 */

namespace app\frontend\modules\deduction;

use app\frontend\models\Goods;
use app\frontend\modules\finance\deduction\PointDeductionSettingManager;
use app\frontend\modules\finance\deduction\PointGoodsDeduction;
use Illuminate\Container\Container;

/**
 * 商品抵扣容器
 * Class GoodsDeductionManager
 * @package app\frontend\modules\deduction
 */
class GoodsDeductionManager extends Container
{
    public function __construct()
    {
        /**
         * 积分抵扣设置模型
         */
        $this->bind('point', function ($deductionSettingManager, $params) {
            /**
             * @var DeductionSettingManagerInterface $aDeductionSettingManager
             */
            $aDeductionSettingManager = app('DeductionManager')->make('DeductionSettingManager')->make('point');
            /**
             * @var PointDeductionSettingManager $aDeductionSettingManager
             */
            $deductionSettingCollection = $aDeductionSettingManager->getDeductionSettingCollection($params[0]);
            return new PointGoodsDeduction($deductionSettingCollection);
        });
    }
}