<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */

namespace app\frontend\modules\order\operations\member;

use app\common\models\DispatchType;
use app\frontend\modules\order\operations\OrderOperation;

class Receive extends OrderOperation
{
    public function getApi()
    {
        // todo 需要提取到门店插件复写的类中,在容器中判断实例哪个类
        if (in_array($this->order->dispatch_type_id, [DispatchType::SELF_DELIVERY, DispatchType::STORE_DELIVERY])) {
            return 'plugin.store-cashier.frontend.store.orderDetail.qrCodeUrl';
        }

        //配送站自提、核销
        if (in_array($this->order->dispatch_type_id, [DispatchType::DELIVERY_STATION_SELF, DispatchType::DELIVERY_STATION_SEND])) {

            return 'plugin.delivery-station.frontend.order.detail.qr-code-url';
        }

        return 'order.operation.receive';
    }
    public function getName()
    {
        // todo 需要提取到门店插件复写的类中,在容器中判断实例哪个类
        if ($this->order->dispatch_type_id == DispatchType::SELF_DELIVERY) {
            // 自提
            return '确认使用';
        }
        if ($this->order->dispatch_type_id == DispatchType::STORE_DELIVERY) {
            // 商家配送
            return '确认核销';
        }

        //配送站自提、核销
        if (in_array($this->order->dispatch_type_id, [DispatchType::DELIVERY_STATION_SELF, DispatchType::DELIVERY_STATION_SEND])) {

            return '确认核销';
        }

        return '确认收货';
    }

    public function getValue()
    {
        // todo 需要提取到门店插件复写的类中,在容器中判断实例哪个类
        if (in_array($this->order->dispatch_type_id, [DispatchType::SELF_DELIVERY, DispatchType::STORE_DELIVERY])) {
            return 'verification_code';
        }

        if (in_array($this->order->dispatch_type_id, [DispatchType::DELIVERY_STATION_SELF, DispatchType::DELIVERY_STATION_SEND])) {
            return 'clerk_code';
        }

        return static::COMPLETE;
    }

    public function enable()
    {
        if ($this->order->dispatch_type_id == DispatchType::HOTEL_CHECK_IN) {
            return false;
        }
        return true;
    }
}