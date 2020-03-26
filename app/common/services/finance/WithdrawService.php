<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/29
 * Time: 上午10:27
 */

namespace app\common\services\finance;


use app\common\facades\Setting;
use Illuminate\Support\Facades\Log;

class WithdrawService
{
    public static function getPayWayService($payWay)
    {
        switch ($payWay) {
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
    public static function getWithdrawStatusName($status)
    {
        switch ($status) {
            case '-1':
                return '审核为无效';
                break;
            case '1':
                return '审核通过';
                break;
        }
    }

    public static function getPayStatusName($status)
    {
        switch ($status) {
            case '0':
                return '打款失败';
                break;
            case '1':
                return '打款成功';
                break;
        }
    }

}