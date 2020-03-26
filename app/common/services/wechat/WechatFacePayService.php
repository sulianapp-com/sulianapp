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
use app\common\services\Pay;
use app\common\models\OrderPay;
use app\common\models\PayType;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayFacePay;
use app\common\services\wechat\lib\WxPayMicroPay;

class WechatFacePayService extends Pay
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

        $op = '微信扫码支付 订单号：' . $data['order_no'];
        $pay_order_model = $this->log(1, '微信扫码支付', $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON, $this->getMemberId());

        /* 支付请求对象 */
        $wxPay = new WxPayFacePay();
        //设置商品或支付单简要描述
        $wxPay->SetBody($data['body']);
        //设置商家数据包，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $wxPay->SetAttach($data['extra']);
        //设置商户系统内部的订单号
        $wxPay->SetOut_trade_no($data['pay_sn']);
        //设置订单总金额
        $wxPay->SetTotal_fee($data['amount']);
        //设置扫码支付授权码
        $wxPay->SetFace_code($data['face_code']);
        $wxPay->SetOpenid($data['openid']);
        $wxPay->SetDevice_info($data['device_info']);

        $response = WxPayApi::facepay(new WxPayConfig(), $wxPay);


        //设置支付参数
        if ($response['result_code'] != 'SUCCESS') {
            // todo 订单取消
            throw new AppException('微信支付失败：'.$response['return_msg']);
        }


        //请求数据日志
        self::payRequestDataLog($data['order_no'], $pay_order_model->type,
            $pay_order_model->third_type, json_encode($response));

        return $response;
    }

    /**
     * 汇聚支付-单笔提现
     *
     * @param \app\common\services\提现者用户ID $member_id
     * @param \app\common\services\提现批次单号 $out_trade_no
     * @param \app\common\services\提现金额 $money
     * @param \app\common\services\提现说明 $desc
     * @param \app\common\services\只针对微信 $type
     * @return mixed
     * @throws AppException
     */
    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        $bank = \Setting::getNotUniacid('plugin.convergePay_set_bankcard');

        $op = '汇聚支付提现 订单号：' . $out_trade_no . '提现金额：' . $money;
        $pay_order_model = $this->withdrawlog(Pay::PAY_TYPE_WITHDRAW, '汇聚提现', $money, $op, $out_trade_no, Pay::ORDER_STATUS_NON, $member_id);

        /* 支付请求对象 */
        $this->setKey($this->set['hmacVal']);
        $this->setGateUrl('https://www.joinpay.com/payment/pay/singlePay');

        //设置支付参数
        $this->setParameter("userNo", $this->set['MerchantNo']);
        $this->setParameter("productCode", $this->set['payment']['productCode']);
        $this->setParameter("requestTime", date('Y-m-d h:i:s'));
        $this->setParameter("merchantOrderNo", $out_trade_no . 'H' . \YunShop::app()->uniacid);
        $this->setParameter("receiverAccountNoEnc", $bank['receiverAccountNoEnc']);
        $this->setParameter("receiverNameEnc", $bank['receiverNameEnc']);
        $this->setParameter("receiverAccountType", $bank['receiverAccountType']);
        $bank['receiverAccountType'] == 204 ? $this->setParameter("receiverBankChannelNo", $bank['receiverBankChannelNo']) : null;
        $this->setParameter("paidAmount", sprintf("%.2f", $money));
        $this->setParameter("currency", $this->set['payment']['currency']);
        //todo 不需要复核
        $this->setParameter("isChecked", 202);
        $this->setParameter("paidDesc", mb_substr($desc, 0, 24));
        $this->setParameter("paidUse", $this->set['payment']['paidUse']);
        $this->setParameter("callbackUrl", $this->notifyWithdrawUrl);
        $this->set['payment']['productCode'] == 'BANK_PAY_COMPOSE_ORDER' ? $this->setParameter("firstProductCode",  "") : null;
        $this->setParameter("hmac", md5($this->parameters['userNo'] . $this->parameters['productCode']
            . $this->parameters['requestTime'] . $this->parameters['merchantOrderNo'] . $this->parameters['receiverAccountNoEnc']
            . $this->parameters['receiverNameEnc'] . $this->parameters['receiverAccountType'] . $this->parameters['receiverBankChannelNo']
            . $this->parameters['paidAmount'] . $this->parameters['currency'] . $this->parameters['isChecked'] . $this->parameters['paidDesc']
            . $this->parameters['paidUse'] . $this->parameters['callbackUrl'] . $this->parameters['firstProductCode']
            . $this->getKey()));

        //请求数据日志
        self::payRequestDataLog($out_trade_no, $pay_order_model->type,
            $pay_order_model->type, json_encode($this->parameters));

        return $this->getWithdrawRequestURL($pay_order_model, $out_trade_no);
    }

    /**
     * 获取微信用户 OpenId
     *
     * @return mixed
     */
    public function getOpenId()
    {
        return McMappingFans::getFansById($this->getMemberId())['openid'];
    }

    /**
     * 获取前台会员登录 ID
     *
     * @return int
     */
    public function getMemberId()
    {
        return \YunShop::app()->getMemberId() ? : 0;
    }

    /**
     * 汇聚-微信或支付宝退款
     *
     * @param \app\common\services\订单号 $out_trade_no
     * @param \app\common\services\订单总金额 $totalmoney
     * @param \app\common\services\退款金额和退款原因 $refundmoney
     * @return bool|mixed
     * @throws AppException
     */
    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        $this->setKey($this->set['hmacVal']);

        $out_refund_no = $this->setUniacidNo(\YunShop::app()->uniacid);
        $op = '汇聚-微信或支付宝退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $refundmoney['price'];

        $pay_type_id = OrderPay::get_paysn_by_pay_type_id($out_trade_no);
        $pay_type_name = PayType::get_pay_type_name($pay_type_id);
        $pay_order_model = $this->refundlog(Pay::PAY_TYPE_REFUND, $pay_type_name, $refundmoney['price'], $op, $out_trade_no, Pay::ORDER_STATUS_NON, 0);

        //设置支付参数
        //head
        $this->setParameter("p1_MerchantNo", $this->set['MerchantNo']);
        $this->setParameter("p2_OrderNo", $out_trade_no. ':' . \YunShop::app()->uniacid);
        $this->setParameter("p3_RefundOrderNo", $out_trade_no. ':' . \YunShop::app()->uniacid);
        $this->setParameter("p4_RefundAmount", sprintf("%.2f", $refundmoney['price']));
        $this->setParameter("p5_RefundReason", $refundmoney['reason']);
        $this->setParameter("p6_NotifyUrl", $this->refundUrlWechat);
        $this->setParameter("hmac", md5($this->parameters['p1_MerchantNo'] . $this->parameters['p2_OrderNo']
            . $this->parameters['p3_RefundOrderNo'] . $this->parameters['p4_RefundAmount'] . $this->parameters['p5_RefundReason']
            . $this->parameters['p6_NotifyUrl'] . $this->getKey()));

        $this->setGateUrl('https://www.joinpay.com/trade/refund.action');

        //请求数据日志
        self::payRequestDataLog($out_trade_no, $pay_order_model->type,
            $pay_order_model->type, json_encode($this->parameters));

        return $this->getRefundRequestUrl($pay_order_model, $out_trade_no);
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
}