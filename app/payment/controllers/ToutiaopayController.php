<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/4/24
 * Time: 下午3:10
 */

namespace app\payment\controllers;



use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\services\Pay;
use app\payment\PaymentController;
use EasyWeChat\Foundation\Application;

class ToutiaopayController extends PaymentController
{
    private $attach = [];

    public function preAction()
    {
        parent::preAction();

        if (empty(\YunShop::app()->uniacid)) {
            $post = $this->getResponseResult();
            if (\YunShop::request()->attach) {
                \Setting::$uniqueAccountId = \YunShop::app()->uniacid = \YunShop::request()->attach;
            } else {
                $this->attach = explode(':', $post['attach']);
                \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[0];
            }
            \Log::debug('---------attach数组--------', \YunShop::app()->uniacid);
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function notifyUrlWechat()
    {
        $post = $this->getResponseResult();
        $this->log($post,'微信支付--TT');

        $verify_result = $this->getWechatSignResult();

        if ($verify_result) {
            $data = [
                'total_fee'    => $post['total_fee'] ,
                'out_trade_no' => $post['out_trade_no'],
                'trade_no'     => $post['transaction_id'],
                'unit'         => 'fen',
                'pay_type'     => '微信支付--TT',
                'pay_type_id'     => 51
            ];

            $this->payResutl($data);
            echo "success";
        } else {
            echo "fail";
        }
    }

    public function notifyUrlAlipay()
    {
        $this->log($_POST, '支付宝支付--TT');

        $verify_result = $this->get_RSA2_SignResult($_POST);

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {

                if (strpos($_POST['out_trade_no'], '_') !== false) {
                    $out_trade_no = substr($_POST['out_trade_no'], strpos($_POST['out_trade_no'], '_')+1);
                } else {
                    $out_trade_no = $_POST['out_trade_no'];
                }

                $data = [
                    'total_fee' => $_POST['total_amount'],
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $_POST['trade_no'],
                    'unit' => 'yuan',
                    'pay_type' => '支付宝支付--TT',
                    'pay_type_id' => 52

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
        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'].'&outtradeno='.\YunShop::request()->outtradeno)->send();
        }

        if (\YunShop::request()->outtradeno) {
            $orderPay = OrderPay::where('pay_sn', \YunShop::request()->outtradeno)->first();
            $orders = Order::whereIn('id', $orderPay->order_ids)->get();
            if (is_null($orderPay)) {
                redirect(Url::absoluteApp('home'))->send();
            }

            //优惠卷分享页
            $share_bool = \app\frontend\modules\coupon\services\ShareCouponService::showIndex($orderPay->order_ids, $orderPay->uid);
            if ($share_bool) {
                $ids = rtrim(implode('_', $orderPay->order_ids), '_');
                redirect(Url::absoluteApp('coupon/share/'.$ids, ['i' => \YunShop::app()->uniacid, 'mid'=> $orderPay->uid]))->send();
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

    /**
     * 微信签名验证
     *
     * @return bool
     */
    public function getWechatSignResult()
    {

        $pay = \Setting::get('shop.pay');
        $app = $this->getEasyWeChatApp($pay);
        $payment = $app->payment;
        $notify = $payment->getNotify();

        //老版本-无参数
        $valid = $notify->isValid();

        if (!$valid) {
            //新版本-有参数
            $valid = $notify->isValid($pay['weixin_apisecret']);
        }

        return $valid;
    }




    /**
     * 支付宝签名验证
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
     * 创建支付对象
     *
     * @param $pay
     * @return \EasyWeChat\Payment\Payment
     */
    public function getEasyWeChatApp($pay)
    {
        $options = [
            'app_id' => $pay['weixin_appid'],
            'secret' => $pay['weixin_secret'],
            // payment
            'payment' => [
                'merchant_id' => $pay['weixin_mchid'],
                'key' => $pay['weixin_apisecret'],
                'cert_path' => $pay['weixin_cert'],
                'key_path' => $pay['weixin_key']
            ]
        ];

        $app = new Application($options);

        return $app;
    }

    /**
     * 获取微信回调结果
     *
     * @return array|mixed|\stdClass
     */
    public function getResponseResult()
    {
        $input = file_get_contents('php://input');
        if (!empty($input) && empty($_POST['out_trade_no'])) {
            //禁止引用外部xml实体
            $disableEntities = libxml_disable_entity_loader(true);

            $data = json_decode(json_encode(simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

            libxml_disable_entity_loader($disableEntities);

            if (empty($data)) {
                exit('fail');
            }
            if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
                exit('fail');
            }
            $post = $data;
        } else {
            $post = $_POST;
        }

        return $post;
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($post,$desc)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['out_trade_no'], $desc, json_encode($post));
    }
}