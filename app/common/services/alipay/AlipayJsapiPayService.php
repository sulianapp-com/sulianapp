<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午12:01
 */

namespace app\common\services\alipay;

use app\common\components\alipay\Wap2\SdkPayment;
use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use app\common\models\PayType;
use app\common\services\alipay\f2fpay\model\AlipayConfig;
use app\common\services\alipay\f2fpay\model\builder\AlipayTradeWapPayContentBuilder;
use app\common\services\alipay\f2fpay\service\AlipayTradeService;
use app\common\services\alipay\MobileAlipay;
use app\common\services\alipay\WebAlipay;
use app\common\services\alipay\WapAlipay;
use app\common\models\Member;
use app\common\services\alipay\AopClient;
use app\common\services\alipay\AlipayTradeRefundRequest;
use app\common\services\Pay;
use app\common\services\PayFactory;

class AlipayJsapiPayService extends Pay
{
    private $_pay = null;
    private $pay_type;

    public function __construct()
    {
        $this->_pay = $this->createFactory();
        $this->pay_type = config('app.pay_type');
    }

    private function createFactory()
    {
        $type = $this->getClientType();
        switch ($type) {
            case 'web':
                $pay = new WebAlipay();
                break;
            case 'mobile':
                $pay = new MobileAlipay();
                break;
            case 'wap':
                $pay = new WapAlipay();
                break;
            default:
                $pay = null;
        }

        return $pay;
    }

    /**
     * 获取客户端类型
     *
     * @return string
     */
    private function getClientType()
    {
        if (Client::isMobile()) {
            return 'wap';
        } elseif (Client::is_app()) {
            return 'mobile';
        } else {
            return 'web';
        }
    }

    /**
     * 订单支付
     * @param array $data
     * @param int $payType
     * @return 提交表单HTML文本|mixed|string
     * @throws \Exception
     */
    public function doPay($data = [], $payType = 2)
    {
        $op = "支付宝订单支付 订单号：" . $data['order_no'];
        $pay_type_name = PayType::get_pay_type_name($payType);
        $this->log($data['extra']['type'], $pay_type_name, $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());

        $alipay_set = \Setting::get('shop.alipay_set');
        $uniacid = substr($data['body'], strrpos($data['body'], ':')+1);
        $alipay = new AlipayTradeWapPayContentBuilder();
        $alipayConfig = new AlipayConfig();
        $config = $alipayConfig->getConfig();

        $appAuthToken = '';
        if (!$alipay_set['app_type']) {
            //第三方应用授权令牌,商户授权系统商开发模式下使用
            $appAuthToken = $alipayConfig->getAuthToken();//根据真实值填写
            $pid = $alipay_set['pid'];//分佣
            $alipay->setSysServiceProviderId($pid);
        }
        $alipay->setOutTradeNo($data['order_no'].'_'.\YunShop::app()->uniacid.'_'.$alipayConfig->getRoyalty());
        $alipay->setTotalAmount($data['amount']);
        $alipay->setSubject(mb_substr($data['subject'], 0, 256));
        $alipay->setBody($uniacid);
        $alipay->setAppAuthToken($appAuthToken);
        $return_url = \Setting::get('alipay-web.return_url');
        $notify_url = Url::shopSchemeUrl('payment/alipay/newNotifyUrl.php');
        $barPay = new AlipayTradeService($config);
        $barPayResult = $barPay->wapPay($alipay, $return_url, $notify_url);

        return $barPayResult;

    }

    public function doRefund($out_trade_no, $totalmoney, $refundmoney='0')
    {
        $out_refund_no = $this->setUniacidNo(\YunShop::app()->uniacid);
        $op = '支付宝退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $totalmoney;
        if (empty($out_trade_no)) {
            throw new AppException('参数错误');
        }
        $pay_type_id = OrderPay::get_paysn_by_pay_type_id($out_trade_no);
        $pay_type_name = PayType::get_pay_type_name($pay_type_id);
        $refund_order = $this->refundlog(Pay::PAY_TYPE_REFUND, $pay_type_name, $totalmoney, $op, $out_trade_no, Pay::ORDER_STATUS_NON, 0);

        //支付宝交易单号
        $pay_order_model = PayOrder::getPayOrderInfo($out_trade_no)->first();
        if ($pay_order_model) {

            $refund_data = array(
                'out_trade_no' => $pay_order_model ->out_order_no,
                'trade_no' => $pay_order_model ->trade_no,
                'refund_amount' => $totalmoney,
                'refund_reason' => '正常退款',
                'out_request_no' => $out_refund_no
            );

            if ($pay_type_id == 10) {
                $result = $this->apprefund($refund_data);
                if ($result) {
                    $this->changeOrderStatus($refund_order, Pay::ORDER_STATUS_COMPLETE, $result['trade_no']);
                    $this->payResponseDataLog($out_trade_no, '支付宝APP退款', json_encode($result));
                    return true;
                } else {
                    return false;
                }
            } else {

                $set = \Setting::get('shop.pay');
                if (isset($set['alipay_pay_api']) && $set['alipay_pay_api'] == 1) {
                    $result =  $this->alipayRefund2($refund_data, $set);
                    if ($result) {
                        $this->changeOrderStatus($refund_order, Pay::ORDER_STATUS_COMPLETE, $result['trade_no']);
                        $this->payResponseDataLog($out_trade_no, '商城支付宝2.0新接口退款', json_encode($result));
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    $alipay = app('alipay.web');
                    $alipay->setOutTradeNo($pay_order_model->trade_no);
                    $alipay->setTotalFee($totalmoney);

                    return $alipay->refund($out_refund_no);
                }
            }
        } else {
            return false;
        }
    }

    private function changeOrderStatus($model, $status, $trade_no)
    {
        $model->status = $status;
        $model->trade_no = $trade_no;
        $model->save();
    }

    public function alipayRefund2($refund_data, $set)
    {
        $aop = new AopClient();
        $request = new AlipayTradeRefundRequest();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = decrypt($set['alipay_app_id']);
        $aop->alipayrsaPublicKey = decrypt($set['rsa_public_key']);
        $aop->rsaPrivateKey = decrypt($set['rsa_private_key']);
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $json = json_encode($refund_data);
        $request->setBizContent($json);
        $result = $aop->execute($request);
        $res = json_decode($result, 1);
        if(!empty($res)&&$res['alipay_trade_refund_response']['code'] == '10000'){
            return $res['alipay_trade_refund_response'];
        } else {
            throw new AppException($res['alipay_trade_refund_response']['msg'] . '-' . $res['alipay_trade_refund_response']['sub_msg']);
        }
    }

    public function apprefund($refund_data)
    {
        $set = \Setting::get('shop_app.pay');
        $aop = new AopClient();
        $request = new AlipayTradeRefundRequest();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $set['alipay_appid'];
        $aop->alipayrsaPublicKey = $set['refund_alipay_sign_public'] ?: $set['alipay_sign_public'];
        $aop->rsaPrivateKey = $set['refund_alipay_sign_private'] ?: $set['alipay_sign_private'];
        $aop->apiVersion = '1.0';
        $aop->signType = $set['refund_newalipay'] == 1 ? 'RSA2' : 'RSA';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $json = json_encode($refund_data);
        $request->setBizContent($json);
        $result = $aop->execute($request);
        $res = json_decode($result, 1);
        if(!empty($res)&&$res['alipay_trade_refund_response']['code'] == '10000'){
            return $res['alipay_trade_refund_response'];
        } else {
            throw new AppException($res['alipay_trade_refund_response']['msg'] . '-' . $res['alipay_trade_refund_response']['sub_msg']);
        }
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc = '', $type=1)
    {
        $batch_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        $op = '支付宝提现 批次号：' . $out_trade_no . '提现金额：' . $money;
        $this->withdrawlog(Pay::PAY_TYPE_REFUND, $this->pay_type[Pay::PAY_MODE_ALIPAY], $money, $op, $out_trade_no, Pay::ORDER_STATUS_NON, $member_id);

        $alipay = app('alipay.web');

        $alipay->setTotalFee($money);

        $member_info = Member::getUserInfos($member_id)->first();

        if ($member_info) {
            $member_info = $member_info->toArray();
        } else {
            throw new AppException('会员不存在');
        }

        if (!empty($member_info['yz_member']['alipay']) && !empty($member_info['yz_member']['alipayname'])) {
            $account = $member_info['yz_member']['alipay'];
            $name = $member_info['yz_member']['alipayname'];
        } else {
            throw new AppException('没有设定支付宝账号');
        }

        return $alipay->withdraw($account, $name, $out_trade_no, $batch_no);
    }

    public function doBatchWithdraw($withdraws)
    {
        $account = [];
        $name    = [];

        $batch_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        foreach ($withdraws as $withdraw) {
            $op = '支付宝提现 批次号：' . $withdraw->withdraw_sn . '提现金额：' . $withdraw->actual_amounts;


            $this->withdrawlog(Pay::PAY_TYPE_REFUND, $this->pay_type[Pay::PAY_MODE_ALIPAY], $withdraw->actual_amounts, $op, $withdraw->withdraw_sn, Pay::ORDER_STATUS_NON, $withdraw->member_id);
        }

        $alipay = app('alipay.web');

        foreach ($withdraws as $withdraw) {
            $member_info = Member::getUserInfos($withdraw->member_id)->first();

            if ($member_info) {
                $member_info = $member_info->toArray();
            } else {
                throw new AppException('会员不存在');
            }

            if (!empty($member_info['yz_member']['alipay']) && !empty($member_info['yz_member']['alipayname'])) {
                $account[] = $member_info['yz_member']['alipay'];
                $name[]    = $member_info['yz_member']['alipayname'];
            } else {
                throw new AppException('没有设定支付宝账号');
            }
        }

        return $alipay->batchWithdraw($account, $name, $withdraws, $batch_no);
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}