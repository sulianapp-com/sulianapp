<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/03/2017
 * Time: 01:07
 */

namespace app\payment\controllers;

use app\backend\modules\refund\services\RefundOperationService;
use app\common\events\finance\AlipayWithdrawEvent;
use app\common\helpers\Url;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use app\common\models\PayRefundOrder;
use app\common\models\PayWithdrawOrder;
use app\common\models\refund\RefundApply;
use app\common\modules\alipay\models\AlipayPayOrder;
use app\common\services\alipay\f2fpay\model\AlipayConfig;
use app\common\services\finance\Withdraw;
use app\common\services\Pay;
use app\payment\PaymentController;
use app\common\models\OrderGoods;
use Yunshop\StoreCashier\common\models\StoreOrder;


class AlipayController extends PaymentController
{
    private $sign_type = ['MD5' => '支付宝', 'RSA' => '支付宝APP', 'RSA2' => '支付宝APP2.0'];

    private $total_fee = ['MD5' => 'total_fee', 'RSA' => 'total_fee', 'RSA2' => 'total_amount'];

    private $pay_type_id = 2;

    public function notifyUrl()
    {

        $this->log($_POST, '支付宝支付');
        if ($_POST['sign_type'] == 'MD5') {
            $verify_result = $this->getSignResult();
        } else {
            //定义app支付类型，验证app回调信息
            $this->pay_type_id = 10;
            $verify_result = $this->get_RSA_SignResult($_POST);
        }

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                if ($_POST['sign_type'] == 'RSA2') {
                    if (strpos($_POST['out_trade_no'], '_') !== false) {
                        $out_trade_no = substr($_POST['out_trade_no'], strpos($_POST['out_trade_no'], '_')+1);
                    } else {
                        $out_trade_no = $_POST['out_trade_no'];
                    }
                } else {
                    $out_trade_no = $_POST['out_trade_no'];
                }

                $total_key = $this->total_fee[$_POST['sign_type']];
                $data = [
                    'total_fee' => $_POST[$total_key],
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $_POST['trade_no'],
                    'unit' => 'yuan',
                    'pay_type' => $this->sign_type[$_POST['sign_type']],
                    'pay_type_id' => $this->pay_type_id

                ];

                $this->payResutl($data);
            }

            echo "success";
        } else {
            echo "fail";
        }
    }

    //商城支付宝app2.0支付异步通知
    public function newNotifyUrl()
    {
        $this->log($_POST, '支付宝支付2.0');
        if ($_POST['sign_type'] == 'MD5') {
            $verify_result = $this->getSignResult();
        } else {
            $verify_result = $this->get_RSA2_SignResult($_POST);
        }

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                if ($_POST['sign_type'] == 'RSA2') {
                    if (strpos($_POST['out_trade_no'], '_') !== false) {
                        $out_trade_no = substr($_POST['out_trade_no'], strpos($_POST['out_trade_no'], '_')+1);
                    } else {
                        $out_trade_no = $_POST['out_trade_no'];
                    }
                } else {
                    $out_trade_no = $_POST['out_trade_no'];
                }

                $total_key = $this->total_fee[$_POST['sign_type']];
                $data = [
                    'total_fee' => $_POST[$total_key],
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $_POST['trade_no'],
                    'unit' => 'yuan',
                    'pay_type' => $this->sign_type[$_POST['sign_type']],
                    'pay_type_id' => $this->pay_type_id

                ];
                $this->payResutl($data);
            }

            echo "success";
        } else {
            echo "fail";
        }
    }

    public function returnUrl()
    {
        if (isset($_GET['alipayresult']) && !empty($_GET['alipayresult'])) {
            $alipayresult = json_decode($_GET['alipayresult'], true);
            if (strpos($alipayresult['alipay_trade_app_pay_response']['out_trade_no'], '_') !== false) {
                $data = explode('_', $alipayresult['alipay_trade_app_pay_response']['out_trade_no']);
                $out_trade_no = $data[1];
                \YunShop::app()->uniacid = $data[0];
            } else {
                $out_trade_no = $alipayresult['alipay_trade_app_pay_response']['out_trade_no'];
            }
            \Log::debug('====================支付宝APP支付2.0======================:', $alipayresult['alipay_trade_app_pay_response']);
        } elseif (strpos($_GET['out_trade_no'], '_') !== false) {
            $data = explode('_', $_GET['out_trade_no']);
            $out_trade_no = $data[1];
            \YunShop::app()->uniacid = $data[0];
            \Log::debug('=============商城支付宝APP支付2.0===========:', $data);
        } else {
            $out_trade_no = $this->substr_var($_GET['out_trade_no']);
        }

        // 拼团订单支付成功后跳转该团页面
        // 插件开启
        if (app('plugins')->isEnabled('fight-groups')) {
            $orderPay = OrderPay::where('pay_sn', $out_trade_no)->first();
            $orders = Order::whereIn('id', $orderPay->order_ids)->get();
            // 只有一个订单
            if ($orders->count() == 1) {
                $order = $orders[0];
                // 是拼团的订单
                if ($order->plugin_id == 54) {
                    $fightGroupsTeamMember = \Yunshop\FightGroups\common\models\FightGroupsTeamMember::uniacid()->with(['hasOneTeam'])->where('order_id', $order->id)->first();
                    // 有团员并且有团队，跳到拼团详情页
                    if (!empty($fightGroupsTeamMember) && !empty($fightGroupsTeamMember->hasOneTeam)) {
                        redirect(Url::absoluteApp('group_detail/' . $fightGroupsTeamMember->hasOneTeam->id, ['i' => \YunShop::app()->uniacid]))->send();
                    } else {
                        redirect(Url::absoluteApp('home'))->send();
                    }
                }
            }
        }

        $trade = \Setting::get('shop.trade');
        //这里做支付后跳转，需要取到支付流水号
        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'].'&outtradeno='.$out_trade_no)->send();
        }

        if ($_GET['sign_type'] == 'MD5') {
            $verify_result = $this->getSignResult();
            if ($verify_result) {
                if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                  
                    redirect(Url::absoluteApp('member/payYes'))->send();
                } else {
                    redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();
                }
            } else {
                redirect(Url::absoluteApp('member/payErr', ['i' => \YunShop::app()->uniacid]))->send();
            }
        } else {
            if ($out_trade_no) {
                $orderPay = OrderPay::where('pay_sn', $out_trade_no)->first();

                $orders = Order::whereIn('id', $orderPay->order_ids)->get();
                if (is_null($orderPay)) {
                    redirect(Url::absoluteApp('home'))->send();
                }
                if ($orders->count() > 1) {
                    redirect(Url::absoluteApp('member/orderlist/', ['i' => \YunShop::app()->uniacid]))->send();
                } else {
                    redirect(Url::absoluteApp('member/orderdetail/'.$orders->first()->id, ['i' => \YunShop::app()->uniacid]))->send();
                }
            } else {
                redirect(Url::absoluteApp('home'))->send();
            }
        }
    }

    public function jsapiNotifyUrl()
    {
        $this->log($_POST, '支付宝支付2.0');
        if ($_POST['sign_type'] == 'MD5') {
            $verify_result = $this->getSignResult();
        } else {
            $verify_result = $this->get_Jsapi_RSA2_SignResult($_POST);
        }

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                if ($_POST['sign_type'] == 'RSA2') {
                    if (strpos($_POST['out_trade_no'], '_') !== false) {
                        $trade_no = explode('_', $_POST['out_trade_no']);
                        $out_trade_no = $trade_no[0];
                    } else {
                        $out_trade_no = $_POST['out_trade_no'];
                    }
                } else {
                    $out_trade_no = $_POST['out_trade_no'];
                }

                $total_key = $this->total_fee[$_POST['sign_type']];
                $data = [
                    'total_fee' => $_POST[$total_key],
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $_POST['trade_no'],
                    'unit' => 'yuan',
                    'pay_type' => $this->sign_type[$_POST['sign_type']],
                    'pay_type_id' => $this->pay_type_id

                ];

                $this->alipayPayResult($data,$trade_no[2]);
                $this->payResutl($data);
            }

            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     * 门店POS分账功能
     * @param $result
     * @param $royalty
     */
    public function alipayPayResult($result, $royalty)
    {
        if (!app('plugins')->isEnabled('store-cashier') || !app('plugins')->isEnabled('face-payment')) {
            return;
        }
        $orderPay = OrderPay::where('pay_sn', $result['out_trade_no'])->first();
        $order = $orderPay->orders->first();
        $store_order = StoreOrder::uniacid()->where('order_id', $order->id)->first();
        $store_id = $store_order->store_id;
        request()->offsetSet('store_id',$store_id);
        $data = [
            'uniacid' => \Yunshop::app()->uniacid,
            'order_id' => $order->id,
            'member_id' => $order->uid,
            'account_id' => request()->store_id,
            'pay_sn' => $result['out_trade_no'],
            'trade_no' => $result['trade_no'],
            'total_amount' => $result['total_amount'],
            'royalty' => $royalty,
        ];
        AlipayPayOrder::create($data);
    }
    //判断返回的数据是否是json格式
    protected function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function refundNotifyUrl()
    {
        \Log::debug('支付宝退款回调');

        $this->refundLog($_POST, '支付宝退款');

        $verify_result = $this->getSignResult();

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['success_num'] >= 1) {
                $plits = explode('^', $_POST['result_details']);

                if ($plits[2] == 'SUCCESS') {
                    $data = [
                        'total_fee' => $plits[1],
                        'trade_no' => $plits[0],
                        'unit' => 'yuan',
                        'pay_type' => '支付宝',
                        'batch_no' => $_POST['batch_no']
                    ];

                    $this->refundResutl($data);
                }
            }

            echo "success";
        } else {
            echo "fail";
        }

    }

    public function withdrawNotifyUrl()
    {
        $data = [];
        \Log::debug('支付宝提现回调');
        $this->withdrawLog($_POST, '支付宝提现');

        $verify_result = $this->getSignResult();

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['success_details']) {
                $post_success_details = explode('|', rtrim($_POST['success_details'], '|'));

                foreach ($post_success_details as $success_details) {
                    $plits = explode('^', $success_details);

                    if ($plits[4] == 'S') {
                        $data[] = [
                            'total_fee' => $plits[3],
                            'trade_no' => $plits[0],
                            'unit' => 'yuan',
                            'pay_type' => '支付宝'
                        ];
                    }
                }

                $this->withdrawResutl($data);
            } elseif ($_POST['fail_details']) {
                $post_fail_details = explode('|', rtrim($_POST['fail_details'], '|'));

                foreach ($post_fail_details as $fail_details) {
                    $plits = explode('^', $fail_details);

                    if ($plits[4] == 'F') {
                        $data[] = [
                            'total_fee' => $plits[3],
                            'trade_no' => $plits[0],
                        ];
                    }
                }

                $this->withdrawFailResutl($data);
            }

            echo "success";
        } else {
            echo "fail";
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        \Log::debug(sprintf('Uniacid[%d]', \YunShop::app()->uniacid));
        $key = \Setting::get('alipay-web.key');
        \Log::debug(sprintf('$key %s', $key));
        $alipay = app('alipay.web');
        $alipay->setSignType('MD5');
        $alipay->setKey($key);

        return $alipay->verify();
    }

    /**
     * app签名验证
     *
     * @return bool
     */
    public function get_RSA_SignResult($params)
    {
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;
        return $this->verify($this->getSignContent($params), $sign);
    }

    /**
     * app2.0签名验证
     *
     * @return bool
     */
    public function get_RSA2_SignResult($params)
    {
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;
        return $this->verify2($this->getSignContent($params), $sign);
    }
    /**
     * app2.0签名验证
     *
     * @return bool
     */
    public function get_Jsapi_RSA2_SignResult($params)
    {
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;
        return $this->verify3($this->getSignContent($params), $sign);
    }

    /**
     * 通过支付宝公钥验证回调信息
     *
     * @param $data
     * @param $sign
     * @return bool
     */
    function verify($data, $sign) {
        $alipay_sign_public = \Setting::get('shop_app.pay.alipay_sign_public');
        //如果isnewalipay为1，则为rsa2支付类型
        $isnewalipay = \Setting::get('shop_app.pay.newalipay');
        if(!$this->checkEmpty($alipay_sign_public)){
            $res = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($alipay_sign_public, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        }
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值
        if ($isnewalipay) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        openssl_free_key($res);
        return $result;
    }


    /**
     * 通过支付宝公钥验证回调信息
     *
     * @param $data
     * @param $sign
     * @return bool
     */
    function verify2($data, $sign) {
        $set = \Setting::get('shop.pay');
        $alipay_sign_public =decrypt($set['rsa_public_key']);
        //如果isnewalipay为1，则为rsa2支付类型
        $isnewalipay = \Setting::get('shop.pay.alipay_pay_api');
        if(!$this->checkEmpty($alipay_sign_public)){
            $res = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($alipay_sign_public, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        }
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值
        if ($isnewalipay) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        openssl_free_key($res);
        return $result;
    }

    /**
     * 通过支付宝公钥验证回调信息
     *
     * @param $data
     * @param $sign
     * @return bool
     */
    function verify3($data, $sign) {
        $set = \Setting::get('shop.alipay_set');
        $alipay_sign_public =decrypt($set['alipay_public_key']);
        //如果isnewalipay为1，则为rsa2支付类型
        if(!$this->checkEmpty($alipay_sign_public)){
            $res = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($alipay_sign_public, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        }
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);

        openssl_free_key($res);
        return $result;
    }

    /**
     * 验证数组重组
     *
     * @param $params
     * @return string
     */
    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, 'UTF-8');

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //              $data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }


        return $data;
    }

    /**
     * 响应日志
     *
     * @param $post
     */
    public function log($post, $desc)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['out_trade_no'], $desc, json_encode($post));
    }

    public function refundLog($post, $desc)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog(0, $desc, json_encode($post));
    }

    public function withdrawLog($post, $desc)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['batch_no'], $desc, json_encode($post));
    }


    /**
     * 支付宝退款回调操作
     *
     * @param $data
     */
    public function refundResutl($data)
    {
        \Log::debug('退款操作', 'refund.succeeded');

        $pay_order = PayOrder::getPayOrderInfoByTradeNo($data['trade_no'])->first();

        if (!$pay_order) {
            return \Log::error('未找到退款订单支付信息', $data);
        }
        $pay_refund_model = PayRefundOrder::getOrderInfo($pay_order->out_order_no);


        if (!$pay_refund_model) {
            return \Log::error('退款订单支付信息保存失败', $data);

        }

        $pay_refund_model->status = 2;
        $pay_refund_model->trade_no = $pay_refund_model->trade_no;
        $pay_refund_model->type = $data['pay_type'];
        $pay_refund_model->save();

        $refundApply = RefundApply::where('alipay_batch_sn',$data['batch_no'])->first();

        if (!isset($refundApply)) {
            return \Log::error('订单退款信息不存在', $data);
        }
        if (!(bccomp($refundApply->price, $data['total_fee'], 2) == 0)) {
            return \Log::error("订单退款金额错误(订单金额:{$refundApply->price}|退款金额:{$data['total_fee']})|比较结果:" . bccomp($refundApply->price, $data['total_fee'], 2) . ")");
        }


        \Log::debug('订单退款(退款申请id:' . $refundApply->id . ',订单id:' . $refundApply->order_id . ')');
        RefundOperationService::refundComplete(['id' => $refundApply->id]);


    }

    /**
     * 支付宝提现回调操作
     *
     * @param $data
     */
    public function withdrawResutl($params)
    {
        if (!empty($params)) {
            foreach ($params as $data ) {
                $pay_refund_model = PayWithdrawOrder::getOrderInfo($data['trade_no']);

                if ($pay_refund_model) {
                    $pay_refund_model->status = 2;
                    $pay_refund_model->trade_no = $data['trade_no'];
                    $pay_refund_model->save();
                }

                \Log::debug('提现操作', 'withdraw.succeeded');

                if (bccomp($pay_refund_model->price, $data['total_fee'], 2) == 0) {
                    Withdraw::paySuccess($data['trade_no']);

                    event(new AlipayWithdrawEvent($data['trade_no']));
                }
            }
        }
    }

    public function withdrawFailResutl($params)
    {
        $trade_no = [];

        if (!empty($params)) {
            foreach ($params as $data ) {
                $pay_refund_model = PayWithdrawOrder::getOrderInfo($data['trade_no']);

                if ($pay_refund_model) {
                    \Log::debug('提现操作', 'withdraw.failed');

                    if (bccomp($pay_refund_model->price, $data['total_fee'], 2) == 0) {
                        Withdraw::payFail($data['trade_no']);
                    }
                }
            }
        }
    }
}