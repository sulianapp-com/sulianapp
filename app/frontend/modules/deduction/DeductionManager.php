<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 上午10:41
 */

namespace app\frontend\modules\deduction;

use Illuminate\Container\Container;

/**
 * 抵扣容器
 * Class DeductionManager
 * @package app\frontend\modules\deduction
 */
class DeductionManager extends Container
{
    public function __construct()
    {
        $this->singleton('DeductionSettingManager', function ($deductionManager, $attributes = []) {
            return new DeductionSettingManager($attributes);
        });
        $this->singleton('GoodsDeductionManager', function ($deductionManager, $attributes = []) {
            return new GoodsDeductionManager($attributes);
        });

    }
}