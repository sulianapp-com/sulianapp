<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/30
 * Time: 下午9:27
 */

namespace app\frontend\modules\finance\services;

class WithdrawService extends \app\common\services\finance\WithdrawService
{ 
    public static function createStatusService($withdraw)
    {
        switch ($withdraw->status) {
            case -1:
                return '无效';
                break;
            case 0:
                return '未审核';
                break;
            case 1:
                return '未打款';
                break;
            case 2:
                return '已打款';
                break;
            case 4:
                return '打款中';
                break;
        }
    }

    public static function createPayWayService($withdraw)
    {
        switch ($withdraw->pay_way) {
            case 'balance':
                return '提现到余额';
                break;
            case 'wechat':
                return '提现到微信';
                break;
            case 'alipay':
                return '提现到支付宝';
                break;
            case 'manual':
                return '提现手动打款';
                break;
        }
    }
}