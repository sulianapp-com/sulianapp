<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/8
 * Time: 下午5:49
 */

namespace app\common\services\finance;


use app\common\facades\Setting;
use app\common\models\PayType;

class IncomeService
{
    private static $pay_way = ['balance', 'wechat', 'alipay', 'manual', 'huanxun', 'eup_pay', 'yop_pay', 'converge_pay'];

    public static function getIncomeWithdrawMode()
    {
        $set = Setting::get('withdraw.income');
        $modeData = [];

        foreach ($set as $key => $item) {
            if (in_array($key, static::$pay_way) && $item) {
                $modeData[$key] = [
                    'name'  => self::getModeName($key),
                    'value' => $key
                ];
            }
        }
        $modeData['service_switch'] = isset($set['service_switch']) ? $set['service_switch'] : 1;
        return $modeData;
    }

    public static function getModeName($key)
    {
        $balance = Setting::get('shop.shop');
        //从数据库获取
        //$balance= empty(PayType::get_pay_type_name(3))?"余额":PayType::get_pay_type_name(3);
        $set = \Setting::get('shop.lang.zh_cn.income');

        $name = '';
        if ($set['name_of_withdrawal']) {
            $name = $set['name_of_withdrawal'];
        } else {
            $name = '提现';
        }
        switch ($key) {
            case 'balance':
                return $name . '到' . $balance['credit'] ?: '余额';
                break;
            case 'wechat':
                return $name . '到微信';
                break;
            case 'alipay':
                return $name . '到支付宝';
                break;
            case 'manual':
                return $name . '手动打款';
                break;
            case 'huanxun':
                return $name . '到银行卡';
                break;
            case 'eup_pay':
                return $name . '到EUP';
            case 'yop_pay':
                return $name . '到易宝';
                break;
            case 'converge_pay':
                return $name . '到银行卡-HJ';
                break;
        }
    }


}