<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午12:00
 */

namespace app\common\services;

use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\McMappingFans;
use app\common\models\Member;
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use app\common\models\PayType;
use app\common\models\Order;
use app\common\services\finance\Withdraw;
use app\frontend\modules\member\services\factory\MemberFactory;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order as easyOrder;

class WechatPay extends Pay
{
    private $pay_type;
    private static $attach_type = 'account';

    public function __construct()
    {
        $this->pay_type = config('app.pay_type');
    }

    public function doPay($data = [], $payType = 1)
    {
        $client_type = null;
        $text = $data['extra']['type'] == 1 ? '支付' : '充值';
        $op = '微信订单' . $text . ' 订单号：' . $data['order_no'];
        $pay_type_name = PayType::get_pay_type_name($payType);
        $pay_order_model = $this->log($data['extra']['type'], $pay_type_name, $data['amount'], $op, $data['order_no'], Pay::ORDER_STATUS_NON, \YunShop::app()->getMemberId());

        if (empty(\YunShop::app()->getMemberId())) {
            throw new AppException('无法获取用户ID');
        }

        if (\YunShop::request()->client_type) {
            $client_type = \YunShop::request()->client_type;
        } else {
            $client_type = $payType;
        }
        $openid = Member::getOpenIdForType(\YunShop::app()->getMemberId(), $client_type);
        \Log::debug('-----pay_member_id-----'. \YunShop::app()->getMemberId());
        //不同支付类型选择参数
        $pay = $this->payParams($payType);
        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])
            || empty($pay['weixin_appid']) || empty($pay['weixin_secret'])) {

            throw new AppException('没有设定支付参数');
        }
        $notify_url = Url::shopSchemeUrl('payment/wechat/notifyUrl.php');
        $app     = $this->getEasyWeChatApp($pay, $notify_url);
        if(isset($_GET['test_uid'])){
            dump($app);
        }
        $payment = $app->payment;
        $data['trade_type'] = $payType == 1 ? 'JSAPI' : 'APP';
        $order = self::getEasyWeChatOrder($data, $openid, $pay_order_model);

        $prepayId = null;
        if ($payType == 1) {
            $result = $payment->prepare($order);
            \Log::debug('预下单', $result->toArray());
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
                $prepayId = $result->prepay_id;

                $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_WAITPAY,'');
            } elseif ($result->return_code == 'SUCCESS') {
                throw new AppException($result->err_code_des);
            } else {
                throw new AppException($result->return_msg);
            }
            $config = $payment->configForJSSDKPayment($prepayId);
            $config['appId'] = $pay['weixin_appid'];

            $js = $app->js->config(array('chooseWXPay'));
            $js = json_decode($js, 1);
            $js['timestamp'] = strval($js['timestamp']);

            return ['config'=>$config, 'js'=>json_encode($js)];
        } else {
            return true;
        }
    }

    /**
     * 微信退款
     *
     * @param 订单号 $out_trade_no
     * @param 订单总金额 $totalmoney
     * @param 退款金额 $refundmoney
     * @return array
     */
    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        $out_refund_no = $this->setUniacidNo(\YunShop::app()->uniacid);
        $op = '微信退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $totalmoney;
        if (empty($out_trade_no)) {
            throw new AppException('参数错误');
        }
        $pay_type_id = OrderPay::get_paysn_by_pay_type_id($out_trade_no);
        $pay_type_name = PayType::get_pay_type_name($pay_type_id);
        $pay_order_model = $this->refundlog(Pay::PAY_TYPE_REFUND, $pay_type_name, $refundmoney, $op, $out_trade_no, Pay::ORDER_STATUS_NON, 0);
        //app退款查询app配置中的微信支付信息
        if ($pay_type_id == 9) {
            $pay = \Setting::get('shop_app.pay');
        } else {
            $pay = $this->payParams();
        }

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])) {
            throw new AppException('没有设定支付参数');
        }

        if (empty($pay['weixin_cert']) || empty($pay['weixin_key'])) {
            throw new AppException('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!');
        }
        $notify_url = '';
        $app     = $this->getEasyWeChatApp($pay, $notify_url);
        $payment = $app->payment;

        try {
            $result = $payment->refund($out_trade_no, $out_refund_no, $totalmoney * 100, $refundmoney * 100);
        } catch (\Exception $e) {
            throw new AppException('微信接口错误:' . $e->getMessage());
        }

        $this->payResponseDataLog($out_trade_no, '微信退款', json_encode($result));
        $status = $this->queryRefund($payment, $out_trade_no);
\Log::debug('---退款状态---', [$status]);
        if ($status == 'PROCESSING' || $status == 'SUCCESS'){
            $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_COMPLETE, $result->transaction_id);
            return true;
        } else {

            throw new AppException('微信接口错误:'.$result->return_msg . '-' . $result->err_code_des . '/' . $status);
        }
    }

    /**
     * 微信提现
     *
     * @param 提现者用户ID $member_id
     * @param 提现金额 $money
     * @param string $desc
     * @param int $type
     * @return array
     */
    public function doWithdraw($member_id, $out_trade_no, $money, $desc='', $type=1)
    {
        //$out_trade_no = $this->setUniacidNo(\YunShop::app()->uniacid);

        $op = '微信钱包提现 订单号：' . $out_trade_no . '提现金额：' . $money;
        $pay_order_model = $this->withdrawlog(Pay::PAY_TYPE_WITHDRAW, $this->pay_type[Pay::PAY_MODE_WECHAT], $money, $op, $out_trade_no, Pay::ORDER_STATUS_NON, $member_id);

        $pay = $this->payParams($type);

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])) {
            throw new AppException('没有设定支付参数');
        }

        if (empty($pay['weixin_cert']) || empty($pay['weixin_key'])) {
            throw new AppException('\'未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!\'');
        }

        $mc_mapping_fans_model = McMappingFans::getFansById($member_id);

        if ($mc_mapping_fans_model) {
            $openid = $mc_mapping_fans_model->openid;
        } else {
            throw new AppException('提现用户不存在');
        }

        $notify_url = '';
        $app = $this->getEasyWeChatApp($pay, $notify_url);

        if ($type == 1) {//钱包
            $merchantPay = $app->merchant_pay;

            $merchantPayData = [
                'partner_trade_no' => empty($out_trade_no) ? time() . Client::random(4, true) : $out_trade_no,
                'openid' => $openid,
                'check_name' => 'NO_CHECK',
                'amount' => $money * 100,
                'desc' => empty($desc) ? '佣金提现' : $desc,
                'spbill_create_ip' => self::getClientIP(),
            ];

            //请求数据日志
            $this->payRequestDataLog($pay_order_model->id, $pay_order_model->type,
                $pay_order_model->type, json_encode($merchantPayData));

            $pay_result = $merchantPay->send($merchantPayData);

            if (isset($pay_result->partner_trade_no)) {
                $result = $merchantPay->query($pay_result->partner_trade_no);

                if ($result->result_code == 'FAIL') {
                    $result = $pay_result;
                }
            } else {
                $result = $pay_result;
            }


        } else {//红包
            $luckyMoney = $app->lucky_money;

            $luckyMoneyData = [
                'mch_billno'       => $pay['weixin_mchid'] . date('YmdHis') . rand(1000, 9999),
                'send_name'        => \YunShop::app()->account['name'],
                're_openid'        => $openid,
                'total_num'        => 1,
                'total_amount'     => $money * 100,
                'wishing'          => empty($desc) ? '佣金提现红包' : $desc,
                'client_ip'        => self::getClientIP(),
                'act_name'         => empty($act_name) ? '佣金提现红包' : $act_name,
                'remark'           => empty($remark) ? '佣金提现红包' : $remark,
            ];

            //请求数据日志
            $this->payRequestDataLog($pay_order_model->id, $pay_order_model->type,
                $pay_order_model->type, json_encode($luckyMoneyData));

            $pay_result = $luckyMoney->sendNormal($luckyMoneyData);

            if (isset($pay_result->mch_billno)) {
                $result = $luckyMoney->query($pay_result->mch_billno);

                if ($result->result_code == 'FAIL') {
                    $result = $pay_result;
                }
            } else {
                $result = $pay_result;
            }

        }

        //响应数据
        $this->payResponseDataLog($pay_order_model->out_order_no, $pay_order_model->type, json_encode($result));
\Log::debug('---提现状态---', [$result->status]);
        if (isset($result->status) && ($result->status == 'PROCESSING' || $result->status == 'SUCCESS' || $result->status == 'SENDING' || $result->status == 'SENT')){
            \Log::debug('提现返回结果', $result->toArray());
            $this->changeOrderStatus($pay_order_model, Pay::ORDER_STATUS_COMPLETE, $result->payment_no);

            $this->payResponseDataLog($out_trade_no, '微信提现', json_encode($result));

            Withdraw::paySuccess($result->partner_trade_no);

            return ['errno' => 0, 'message' => '微信提现成功'];
        } else {
            return ['errno' => 1, 'message' => '微信接口错误:' . $result->return_msg . '-' . $result->err_code_des];
        }
    }

    /**
     * 构造签名
     *
     * @var void
     */
    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }

    /**
     * 创建支付对象
     *
     * @param $pay
     * @return \EasyWeChat\Payment\Payment
     */
    public function getEasyWeChatApp($pay, $notify_url)
    {
        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret'],
            // payment
            'payment' => [
                'merchant_id'        => $pay['weixin_mchid'],
                'key'                => $pay['weixin_apisecret'],
                'cert_path'          => $pay['weixin_cert'],
                'key_path'           => $pay['weixin_key'],
                'notify_url'         => $notify_url
            ]
        ];
        $app = new Application($options);

        return $app;
    }

    /**
     * 创建预下单
     *
     * @param $data
     * @param $openid
     * @param $pay_order_model
     * @return easyOrder
     */
    public static function getEasyWeChatOrder($data, $openid, &$pay_order_model)
    {
        $attributes = [
            'trade_type'       => $data['trade_type'], // JSAPI，NATIVE，APP...
            'body'             => mb_substr($data['subject'], 0, 120),
            'out_trade_no'     => $data['order_no'],
            'total_fee'        => $data['amount'] * 100, // 单位：分
            'nonce_str'        => Client::random(8) . "",
            'device_info'      => 'yun_shop',
            'attach'           => \YunShop::app()->uniacid . ':' . self::$attach_type,
            'spbill_create_ip' => self::getClientIP(),
            'openid'           => $openid
        ];

        //请求数据日志
        self::payRequestDataLog($attributes['out_trade_no'], $pay_order_model->type,
            $pay_order_model->third_type, json_encode($attributes));

        return new easyOrder($attributes);
    }

    private function changeOrderStatus($model, $status, $trade_no)
    {
        $model->status = $status;
        $model->trade_no = $trade_no;
        $model->save();
    }

    /**
     * 支付参数
     * @return array|mixed
     */
    private function payParams($payType = 1)
    {
        $pay = \Setting::get('shop.pay');

        if (request()->input('type') == MemberFactory::LOGIN_MINI_APP || \YunShop::request()->app_type == 'wechat') {
            self::$attach_type = 'wechat';

            $min_set = \Setting::get('plugin.min_app');

            if (is_null($min_set) || 0 == $min_set['switch']) {
                throw new AppException('未开启小程序');
            }

            if (empty($min_set['mchid']) || empty($min_set['api_secret']) ) {
                throw new AppException('未设置支付参数');
            }

            $pay = [
                'weixin_appid' => $min_set['key'],
                'weixin_secret' => $min_set['secret'],
                'weixin_mchid' => $min_set['mchid'],
                'weixin_apisecret' => $min_set['api_secret'],
                'weixin_cert' => '',
                'weixin_key' => ''
            ];
        } elseif ($payType == 9){
            $pay = \Setting::get('shop_app.pay');
        } else {
            $pay = \Setting::get('shop.pay');
        }

        return $pay;
    }

    /**
     * 订单退款查询
     *
     * @param $payment
     * @param $out_trade_no
     * @return mixed
     */
    public function queryRefund($payment, $out_trade_no)
    {
        $result = $payment->queryRefund($out_trade_no);

        foreach ($result as $key => $value) {
            if (preg_match('/refund_status_\d+/', $key)) {
                return $value;
            }
        }

        return 'fail';
    }
}