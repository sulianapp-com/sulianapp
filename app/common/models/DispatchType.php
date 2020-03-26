<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:32
 */

namespace app\common\models;

/**
 * Class DispatchType
 * @package app\common\models
 * @property int need_send
 */
class DispatchType extends BaseModel
{
    public $table = 'yz_dispatch_type';
    const EXPRESS = 1; // 快递
    const SELF_DELIVERY = 2; // 自提
    const STORE_DELIVERY = 3; // 门店配送
    const HOTEL_CHECK_IN  = 4; // 酒店入住

    const DELIVERY_STATION_SELF  = 5; // 配送站自提

    const DELIVERY_STATION_SEND  = 6; // 配送站送货

    const DRIVER_DELIVERY  = 7; //司机配送

    const PACKAGE_DELIVER = 8; //自提点

    public function needSend(){
        return $this->need_send;
    }

    public static function getExpressDelivery()
    {

        $boolNum = \Setting::get('shop.trade.is_dispatch');
        if ($boolNum) {
            return [];
        }

        return [
            'dispatch_type_id' => 1,
            'name' => '快递',
        ];
    }
}