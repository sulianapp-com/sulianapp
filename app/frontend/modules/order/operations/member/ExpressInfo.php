<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */
namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;
use app\common\models\DispatchType;

class ExpressInfo extends OrderOperation
{
    public function getApi()
    {
        return 'dispatch.express';
    }
    public function getName()
    {
        return '物流信息';
    }

    public function getValue()
    {
        return static::EXPRESS;
    }
    public function enable()
    {
        // 虚拟
        if ($this->order->isVirtual()) {
            return false;
        }
        
        // todo 这里要修改，不然每次有新的都得往这加
        // 门店自提、配送站自提、配送站送货
        $dispatchType = [
            DispatchType::SELF_DELIVERY, 
            DispatchType::DELIVERY_STATION_SELF, 
            DispatchType::DELIVERY_STATION_SEND,
            DispatchType::PACKAGE_DELIVER,
        ];
        if (in_array($this->order->dispatch_type_id, $dispatchType)) {
            return false;
        }
        
        return true;
    }
}