<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/25
 * Time: 9:38
 */

namespace app\common\services\plugin;


class DeliveryDriverSet
{
    //是否开启
    public static function whetherEnabled()
    {
        if (app('plugins')->isEnabled('delivery-driver')) {
            $set = \Setting::get('plugin.delivery_driver');
            return intval($set['is_open']);
        }

        return 0;
    }

}