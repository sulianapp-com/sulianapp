<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 下午3:28
 */

namespace app\frontend\modules\finance\deduction;

use app\frontend\modules\deduction\GoodsDeduction;

class PointGoodsDeduction extends GoodsDeduction
{
    public function getCode()
    {
        return 'point';
    }

    public function deductible($goods)
    {
        return true;
    }
}