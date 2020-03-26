<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/5
 * Time: 下午7:36
 */

namespace app\common\models\finance;


use app\common\models\Order;

class IncomeOrder extends Order
{
    public function commissionorders()
    {
        return $this->morphMany('Yunshop\Commission\models\CommissionOrder', 'ordertable');
    }
}