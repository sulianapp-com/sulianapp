<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 上午10:41
 */

namespace app\frontend\modules\deduction;

use app\common\models\Goods;

interface DeductionSettingManagerInterface
{
    /**
     * @param Goods $goods
     * @return DeductionSettingCollection
     */
    public function getDeductionSettingCollection(Goods $goods);
}