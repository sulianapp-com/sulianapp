<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/22
 * Time: 上午11:45
 */

/**
 * PC客户端移动支付功能
 */
namespace app\common\services\alipay;


use app\common\services\AliPay;

class WebAlipay extends AliPay
{
    public function __construct()
    {}

    public function doPay($data, $payType = 2)
    {
        // 创建支付单。
        $alipay = app('alipay.web');

        $alipay->setOutTradeNo($data['order_no']);
        $alipay->setTotalFee($data['amount']);
        $alipay->setSubject($data['subject']);
        $alipay->setBody($data['body']);

        //$alipay->setQrPayMode('4'); //该设置为可选，添加该参数设置，支持二维码支付。

        // 跳转到支付页面。
        return $alipay->getPayLink();
    }
}