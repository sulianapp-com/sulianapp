<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 上午10:41
 */

namespace app\frontend\modules\deduction;

use app\frontend\modules\finance\deduction\PointDeductionSettingManager;
use Illuminate\Container\Container;

/**
 * 抵扣设置类容器
 * Class DeductionSettingManager
 * @package app\frontend\modules\deduction
 */
class DeductionSettingManager extends Container
{
    public function __construct()
    {
        /**
         * 积分抵扣设置模型
         */
        $this->singleton('point', function ($deductionSettingManager) {
            return new PointDeductionSettingManager();
        });
    }
}