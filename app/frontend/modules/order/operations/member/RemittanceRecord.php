<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午5:37
 */

namespace app\frontend\modules\order\operations\member;


use app\common\models\PayType;
use app\frontend\modules\order\operations\OrderOperation;

class RemittanceRecord extends OrderOperation
{
    public function getApi()
    {
        return 'remittance.remittance-record';
    }
    public function getName()
    {
        return '转账信息';
    }
    public function getValue()
    {
        return static::REMITTANCE_RECORD;
    }
    public function enable()
    {
        if($this->order->pay_type_id == PayType::REMITTANCE){
            return true;
        }
        return false;
    }
}