<?php

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\payment\PaymentController;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\common\services\Pay;
use app\common\models\Order;
use app\common\models\OrderPay;

class DianbangscanController extends PaymentController
{
    private $parameters = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $this->parameters = $_POST;
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->parameters['billDesc'];
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    //微信公众号支付通知
    public function notifyUrl()
    {
        \Log::debug('------------店帮微信异步通知---------------->');

        $this->log($this->parameters, '店帮微信');

        $set = \Setting::get('plugin.dian-bang-scan');
        $this->setKey($set['secret']);
        $order_no = explode('-', $this->getParameter('billNo'));

        if($this->verify($this->parameters)) {
            $billPayment = json_decode($this->parameters['billPayment'],true);
            if ($billPayment['status'] == 'TRADE_SUCCESS') {
                \Log::debug('-------店帮微信支付开始---------->');
                $data = [
                    'total_fee'    => floatval($this->getParameter('totalAmount')),
                    'out_trade_no' => $order_no[1],
                    'trade_no'     => $billPayment['targetOrderId'],
                    'unit'         => 'fen',
                    'pay_type'     => '店帮微信支付',
                    'pay_type_id'  => 24,
                ];
                $this->payResutl($data);
                \Log::debug('<---------店帮微信支付结束-------');
                echo 'SUCCESS';
                exit();
            } else {
                //支付失败
                echo 'FAILED';
                exit();
            }
        } else {
            //签名验证失败
            echo 'FAILED';
            exit();
        }
    }

    //支付宝支付通知
//    public function alipayNotifyUrl()
//    {
//        \Log::debug('------------店帮支付宝异步通知---------------->');
//        $this->log($this->parameters, '店帮支付宝');
//
//        $set = \Setting::get('plugin.dian-bang-scan');
//        $this->setKey($set['key']);
//
//        if($this->getSignResult()) {
//            \Log::info('------店帮支付宝验证成功-----');
//            if ($this->getParameter('status') == 0 && $this->getParameter('result_code') == 0) {
//                \Log::info('-------店帮支付宝支付开始---------->');
//                $data = [
//                    'total_fee'    => floatval($this->getParameter('total_fee')),
//                    'out_trade_no' => $this->getParameter('out_trade_no'),
//                    'trade_no'     => 'dian-bang-scan',
//                    'unit'         => 'fen',
//                    'pay_type'     => '店帮支付宝',
//                    'pay_type_id'  => 24,
//                ];
//                $this->payResutl($data);
//                \Log::info('<---------店帮支付宝支付结束-------');
//                echo 'success';
//                exit();
//            } else {
//                //支付失败
//                echo 'failure';
//                exit();
//            }
//        } else {
//            //签名验证失败
//            echo 'failure';
//            exit();
//        }
//    }

    public function returnUrl()
    {
        \Log::debug('<--------_GET-------->',$_GET);

        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'])->send();
        }


        redirect(Url::absoluteApp('member/payYes', ['i' => $_GET['i']]))->send();
    }



    /**
     * 验证签名是否正确
     * @param $data
     * @return bool
     */
    function verify($data) {
        //返回参数生成sign
        $signType = empty($data['signType']) ? 'md5' : $data['signType'];
        $sign = $this->generateSign($data, $signType);

        //返回的sign
        $returnSign = $data['sign'];

        if ($returnSign != $sign) {
            return false;
        }

        return true;
    }

    public function generateSign($params, $signType = 'md5') {
        return $this->sign($this->getSignContent($params), $signType);
    }

    /**
     * 生成signString
     * @param $params
     * @return string
     */
    public function getSignContent($params) {
        //sign不参与计算
        $params['sign'] = '';
        //排序
        ksort($params);
        $paramsToBeSigned = [];
        foreach ($params as $k=>$v) {
            if ($v !== '')
            {
                if (is_array($v))
                {
                    $paramsToBeSigned[] = $k.'='.str_replace("\\/", "/", json_encode($v,JSON_UNESCAPED_UNICODE));
                }else{
                    $paramsToBeSigned[] = $k.'='.$v;
                }
            }
        }
        unset ($k, $v);

        //签名字符串
        $stringToBeSigned = implode('&', $paramsToBeSigned);
        $stringToBeSigned .= $this->key;

        return $stringToBeSigned;
    }

    /**
     * 生成签名
     * @param $data
     * @param string $signType
     * @return string
     */
    protected function sign($data, $signType = "md5") {
        $sign = hash($signType, $data);

        return strtoupper($sign);
    }



    /**
     *设置密钥
     */
    public function setKey($key) {
        $this->key = $key;
    }


    /**
     * @param 获取密钥
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     *获取参数值
     */
    public function getParameter($parameter) {
        return isset($this->parameters[$parameter])?$this->parameters[$parameter] : '';
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($data, $msg = '店帮支付')
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($this->getParameter('out_trade_no'), $msg, json_encode($data));
    }
}