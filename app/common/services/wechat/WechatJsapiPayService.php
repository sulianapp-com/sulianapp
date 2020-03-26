<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/11/29
 * Time: 19:26
 */

namespace app\common\services\wechat;


use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\models\PayType;
use app\common\services\Pay;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayException;
use app\common\services\wechat\lib\WxPayJsApiPay;
use app\common\services\wechat\lib\WxPayUnifiedOrder;
use EasyWeChat\Foundation\Application;

class WechatJsapiPayService extends Pay
{

    private static $attach_type = 'account';
    /**
     * 订单支付
     * @param array $data
     * @param int $payType
     * @return array|bool|mixed
     * @throws AppException
     */
    public function doPay($data = [], $payType = 48)
    {
        $client_type = null;
        $op = '微信订单支付订单号：' . $data['order_no'];
        $pay_type_name = PayType::get_pay_type_name($payType);
        $pay_order_model = $this->log(1, $pay_type_name, $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());

        if (empty(\YunShop::app()->getMemberId())) {
            throw new AppException('无法获取用户ID');
        }

        $client_type = \YunShop::request()->client_type ?: $payType;

        $openid = Member::getOpenIdForType(\YunShop::app()->getMemberId(), $client_type);
        $data['trade_type'] = 'JSAPI';
        /* 支付请求对象 */
        $wxPay = new WxPayUnifiedOrder();
        //设置商品或支付单简要描述
        $wxPay->SetBody(mb_substr($data['subject'], 0, 120));
        //设置商家数据包，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $wxPay->SetAttach(\YunShop::app()->uniacid . ':' . self::$attach_type .':'.(new WxPayConfig())->GetProfitSharing().':'.request()->store_id);
        //设置商户系统内部的订单号
        $wxPay->SetOut_trade_no($data['order_no']);
        //设置订单总金额
        $wxPay->SetTotal_fee($data['amount'] * 100);
        $wxPay->SetDevice_info('yun_shop');
        $wxPay->SetOpenid($openid);
//        $wxPay->SetAttach((new WxPayConfig())->GetProfitSharing());
        $wxPay->SetTrade_type($data['trade_type']);
        $wxPay->SetNotify_url(Url::shopSchemeUrl('payment/wechat/jsapiNotifyUrl.php'));
        //请求数据日志
        self::payRequestDataLog($data['order_no'], $pay_order_model->type, $pay_order_model->third_type, json_encode($data));
        $result = WxPayApi::unifiedOrder(new WxPayConfig(), $wxPay);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
            $pay_order_model->status =  Pay::ORDER_STATUS_WAITPAY;
            $pay_order_model->trade_no = '';
            $pay_order_model->save();
        } elseif ($result['return_code'] == 'SUCCESS') {
            throw new AppException($result['err_code_des']);
        } else {
            throw new AppException($result['return_msg']);
        }

        $config = $this->GetJsApiParameters($result);

//        $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//        $nonce = WxPayApi::getNonceStr();
//        $ticket = $this->ticket();
//        $timestamp = time();
//        $signPackage = [
//            'appId' => $result['appid'],
//            'nonceStr' => $nonce,
//            'timestamp' => $timestamp,
//            'url' => $url,
//            'signature' => sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}"),
//            'debug' => false,
//            'beta' => false,
//            'jsApiList' => ['chooseWXPay']
//        ];



        return [
            'config'=>$config,
//            'js'=>json_encode($signPackage)
        ];

    }

    /**
     * 退款
     *
     * @param $out_trade_no 订单号
     * @param $totalmoney 订单总金额
     * @param $refundmoney 退款金额
     * @return mixed
     */
    function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        // TODO: Implement doRefund() method.
    }

    /**
     * 提现
     *
     * @param $member_id 提现者用户ID
     * @param $out_trade_no 提现批次单号
     * @param $money 提现金额
     * @param $desc 提现说明
     * @param $type 只针对微信 1-企业支付(钱包) 2-红包
     * @return mixed
     */
    function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    /**
     * 构造签名
     *
     * @return mixed
     */
    function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }

    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return array 数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }

        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetSignType("MD5");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);

        $config = new WxPayConfig();
        $jsapi->SetPaySign($jsapi->MakeSign($config));
        $parameters = $jsapi->GetValues();
        return $parameters;
    }
}