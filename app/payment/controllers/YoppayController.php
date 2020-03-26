<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/22
 * Time: 9:51
 */

namespace app\payment\controllers;

use app\payment\PaymentController;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use Yunshop\YopPay\models\YopOrderRefund;
use Yunshop\YopPay\models\YopPayOrder;

class YoppayController extends PaymentController
{
    protected $set;

    protected  $parameters;

    public function __construct()
    {
        parent::__construct();

        $this->set = $this->getMerchantNo();

        if (empty($this->set)) {
            exit('商户不存在');
        }
        if (empty(\YunShop::app()->uniacid)) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->set['uniacid'];
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
        if (!app('plugins')->isEnabled('yop-pay')) {
            echo 'Not turned on yop pay';
            exit();
        }

        $this->dealWith();
    }

    protected function getMerchantNo()
    {
        $app_key = $_REQUEST['customerIdentification'];
        $merchant_no = substr($app_key,  strrpos($app_key, 'OPR:')+4);
        $set = DB::table('yz_yop_setting')->where('merchant_no', $merchant_no)->first();

        return $set;
    }

    private function dealWith()
    {
        $yop_data = $_REQUEST['response'];

        if ($yop_data) {
            $response = \Yunshop\YopPay\common\Util\YopSignUtils::decrypt($yop_data, $this->set['son_private_key'], $this->set['yop_public_key']);
            $this->parameters = json_decode($response, true);
        }
    }

    //错误日志
    protected function yopLog($desc,$error,$data)
    {
        \Yunshop\YopPay\common\YopLog::yopLog($desc, $error,$data);
    }

    //返回日志
    protected function yopResponse($desc,$params, $type = 'unify')
    {
        \Yunshop\YopPay\common\YopLog::yopResponse($desc, $params, $type);
    }

    //返回日志
    protected function debug($desc, $params = [])
    {
        \Yunshop\YopPay\common\YopLog::debug($desc, $params);
    }

    //异步支付通知
    public function notifyUrl()
    {
        $this->log($this->set['merchant_no'], $this->parameters);

        $this->yopResponse('支付通知pay_sn:'.$this->getParameter('orderId'), $this->parameters, 'pay');

        $pay_order = YopPayOrder::paySn($this->getParameter('orderId'))->first();
        if (!$pay_order) {
            $this->savePayOrder();
        }

        $data = [
            'total_fee'    => floatval($this->getParameter('orderAmount')),
            'out_trade_no' => $this->getParameter('orderId'),
            'trade_no'     => $this->getParameter('uniqueOrderNo'),
            'unit'         => 'yuan',
            'pay_type'     => '易宝微信',
            'pay_type_id'  => 26,
        ];

        //支付产品为用户扫码 支付类型为支付宝
        if ($this->paymentProduct() == YopPayOrder::SCCANPAY && $this->platformType() == YopPayOrder::TYPE_ALIPAY) {
            $data['pay_type'] = '易宝支付宝';
            $data['pay_type_id'] = 32;
        }

        $this->payResutl($data);

        echo 'SUCCESS';
        exit();
    }

    protected function savePayOrder()
    {
        $data = [
            'uniacid'=> \YunShop::app()->uniacid,
            'pay_sn' => $this->getParameter('orderId'),
            'yop_order_no' => $this->getParameter('uniqueOrderNo'),
            'order_amount' => $this->getParameter('orderAmount'),
            'total_amount' => $this->getParameter('payAmount'),
            'can_divide_amount' => $this->getParameter('payAmount'),
            'rate' =>  $this->set['rate'],
            'rate_amount'  => $this->rateAmount(),
            'pay_at' => strtotime($this->getParameter('paySuccessDate')),
            'platform_type' => $this->platformType(),
            'payment_product' => $this->paymentProduct(),
        ];

        $yop_order =  new YopPayOrder();

        $yop_order->fill($data);

        $yop_order->save();
    }

    //平台分类 支付类型
    protected function platformType()
    {
        $status = 0;
        if (!empty($this->parameters['platformType'])) {
            switch ($this->parameters['platformType']) {
                case 'WECHAT': //微信
                    $status = YopPayOrder::TYPE_WECHAT;
                    break;
                case 'ALIPAY': //支付宝
                    $status = YopPayOrder::TYPE_ALIPAY;
                    break;
                case 'NET':
                    $status = YopPayOrder::TYPE_NET;
                    break;
                case 'NCPAY':
                    $status = YopPayOrder::TYPE_NCPAY;
                    break;
                case 'CFL':
                    $status = YopPayOrder::TYPE_CFL;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
    }

    //支付产品
    protected function paymentProduct()
    {
        $status = 0;
        if (!empty($this->parameters['paymentProduct'])) {
            switch ($this->parameters['paymentProduct']) {
                case 'WECHAT_OPENID': //微信公众号
                    $status = YopPayOrder::WECHAT_OPENID;
                    break;
                case 'SCCANPAY': //用户扫码
                    $status = YopPayOrder::SCCANPAY;
                    break;
                case 'MSCANPAY': //商家扫码
                    $status = YopPayOrder::MSCANPAY;
                    break;
                case 'ZFB_SHH': //支付宝生活号
                    $status = YopPayOrder::ZFB_SHH;
                    break;
                case 'ZF_ZHZF': //商户账户支付
                    $status = YopPayOrder::ZF_ZHZF;
                    break;
                case 'EWALLETH5': //钱包H5支付
                    $status = YopPayOrder::EWALLETH5;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
    }

    protected function rateAmount()
    {
        $rate =  $this->set['rate'];
        $rate_amount = bcmul($this->getParameter('orderAmount'), bcdiv($rate, 100,4), 2);

        return max($rate_amount, 0);
    }

    //同步通知
    public function redirectUrl()
    {
//        $yop_data = $_REQUEST;
        $this->debug('<-----------易宝同步通知---------------->', $_REQUEST);
        //$url = str_replace('https','http', Url::shopSchemeUrl("?menu#/member/payYes?i={$uniacid}"));
        //redirect($url)->send();
    }

    //订单超时通知地址
    public function timeoutNotifyUrl()
    {
        $this->debug('<-------易宝支付订单超时通知-------------->', $this->parameters);
    }

    //订单清算通知地址
    public function csUrl()
    {
        $yop_order =  YopPayOrder::csAnnal($this->getParameter('uniqueOrderNo'),  $this->getParameter('orderId'))->first();

        if (!$yop_order) {
            $this->yopLog('订单清算','易宝支付订单不存在无法清算',$this->parameters);
            exit('Record does not exist');
        }

        $this->yopResponse('订单清算pay_sn:'.$this->getParameter('orderId'), $this->parameters, 'cs');


        $data = [
            'status' => 1,
            'cs_at' => strtotime($this->getParameter('csSuccessDate')),
            'merchant_fee' => $this->getParameter('merchantFee'),
            'customer_fee' => $this->getParameter('customerFee'),
        ];

        $yop_order->fill($data);

        $yop_order->save();

        echo 'SUCCESS';exit();

    }

    //订单退款
    public function refundUrl()
    {
        $this->debug('-------------易宝订单退款通知---------------');
        $yop_refund = YopOrderRefund::getRefundAnnal($this->getParameter('orderId'), $this->getParameter('refundRequestId'))
            ->where('status', YopOrderRefund::REFUND)->first();

        if (!$yop_refund) {
            $this->yopLog('退款不存在pay_sn:'.$this->getParameter('orderId'),'易宝订单退款记录不存在',$this->parameters);
            exit('Record does not exist');
        }

        if ($yop_refund->status != YopOrderRefund::REFUND) {
            $this->debug('<-----------退款已处理---------------->', ['id'=>$yop_refund->id, 'pay_sn'=>$this->getParameter('orderId')]);
            echo 'SUCCESS';exit();
        }

        $this->yopResponse('退款通知pay_sn:'.$this->getParameter('orderId'), $this->parameters, 'refund');

        $yop_refund->status = $this->refundStatus();

        if ($this->getParameter('refundSuccessDate')) {
            $yop_refund->refund_at = strtotime($this->getParameter('refundSuccessDate'));
        }

        if ($this->getParameter('errorMessage')) {
            $yop_refund->error_message = $this->getParameter('errorMessage');
        }

        $bool = $yop_refund->save();

        if ($bool) {
            $yop_order =  YopPayOrder::paySn($this->getParameter('orderId'))->where('can_refund', YopOrderRefund::REFUND_APPLY)->first();
            if ($yop_order) {
                $yop_order->refund_amount = bcadd($yop_order->refund_amount, $this->getParameter('refundAmount'), 2);
                $yop_order->can_refund = YopOrderRefund::REFUND;
                $yop_order->save();
            }
        }
        
        echo 'SUCCESS';exit();
    }

    //支付产品
    protected function refundStatus()
    {
        $status = 0;
        if (!empty($this->parameters['status'])) {
            switch ($this->parameters['status']) {
                case 'FAILED':
                    $status = YopOrderRefund::REFUND_FAILED;
                    break;
                case 'SUCCESS':
                    $status = YopOrderRefund::REFUND_SUCCESS;
                    break;
                case 'CANCEL':
                    $status = YopOrderRefund::REFUND_CANCEL;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
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
    public function log($out_trade_no, $data, $msg = '易宝支付')
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($out_trade_no, $msg, json_encode($data));
    }
}