<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/10
 * Time: 9:16 AM
 */

namespace app\frontend\modules\deduction;


use app\frontend\models\order\PreOrderDeduction;

class InvalidOrderDeduction extends PreOrderDeduction
{
    public function isChecked()
    {
        return false;
    }

}