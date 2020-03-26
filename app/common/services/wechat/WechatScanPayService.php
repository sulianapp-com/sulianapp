<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/6/3
 * Time: 下午3:10
 */

namespace app\common\services\wechat;


use app\common\exceptions\AppException;
use app\common\helpers\Url;
use app\common\models\McMappingFans;
use app\common\models\PayOrder;
use app\common\services\Pay;
use app\common\models\OrderPay;
use app\common\models\PayType;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayMicroPay;
use app\common\services\wechat\lib\WxPayOrderQuery;

class WechatScanPayService extends Pay
{
    public $set = null;
    public $config = null;

    public function __construct()
    {
        $this->config = new WxPayConfig();
        $this->set = $set = \Setting::get('shop.wechat_set');
    }

    /**
     * 支付
     * @param array $data
     * @return mixed|string
     * @throws AppException
     * @throws \app\common\services\wechat\lib\WxPayException
     */

    public function doPay($data = [])
    {
        if (\YunShop::request()->type != 9) {
            throw new AppException('不是商家APP 微信扫码支付不可用');
        }

        $pay_name = $data['pay_type'] == 'wechat_scan' ? '微信扫码支付' : '微信人脸支付';
        $op = $pay_name.' 订单号：' . $data['order_no'];
        $pay_order_model = $this->log(1, $pay_name, $data['amount'] / 100, $op, $data['order_no'], Pay::ORDER_STATUS_NON, $this->getMemberId());

        /* 支付请求对象 */
        $wxPay = new WxPayMicroPay();
        //设置商品或支付单简要描述
        $wxPay->SetBody($data['body']);
        //设置商家数据包，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $wxPay->SetAttach($data['extra']);
        //设置商户系统内部的订单号
        $wxPay->SetOut_trade_no($data['pay_sn']);
        //设置订单总金额
        $wxPay->SetTotal_fee($data['amount']);
        //设置扫码支付授权码
        $wxPay->SetAuth_code($data['auth_code']);

        $response = WxPayApi::micropay(new WxPayConfig(), $wxPay);

        if ($response['result_code'] != 'SUCCESS') {
            // todo 订单取消
            throw new AppException('微信支付失败：'.$response['return_msg']);
        }

        //更新openid
        $response = $this->setOpenId($response);
        $response['profit_sharing'] = (new WxPayConfig())->GetProfitSharing() == 'Y' ?1:0;
        //请求数据日志
        self::payRequestDataLog($data['order_no'], $pay_order_model->type,
            $pay_order_model->third_type, json_encode($response));

        return $response;
    }

    /**
     * 提现
     */
    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {

    }

    /**
     * 支付回调操作
     *
     * @param $data
     */
    public function payResult($data)
    {

    }

    /**
     * 退款
     */
    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {

    }

    public function getMemberId()
    {
        return \YunShop::app()->getMemberId() ? : 0;
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
     *获取带参数的请求URL
     */
    function getRequestURL() {

        $this->buildRequestSign();

        $reqPar =json_encode($this->parameters);
        \Log::debug('-----请求参数----', $reqPar);

        $requestURL = $this->getGateURL() . "?data=".base64_encode($reqPar);

        return $requestURL;
    }

    function setOpenId($data)
    {
        if (!$this->set['is_independent'] && $this->set['sub_appid'] && $this->set['sub_mchid']) {
            $data['openid'] = $data['sub_openid'];
        }
        return $data;
    }
}