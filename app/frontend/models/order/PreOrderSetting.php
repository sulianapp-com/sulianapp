<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;


use app\frontend\modules\order\models\PreOrder;

class PreOrderSetting extends \app\common\models\order\OrderSetting
{
    public $order;

    public function setOrder(PreOrder $order)
    {
        $this->order = $order;
        $order->orderSettings->push($this);
    }
}