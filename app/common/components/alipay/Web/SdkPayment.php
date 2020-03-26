<?php

namespace app\common\components\alipay\Web;

use app\common\events\finance\AlipayWithdrawEvent;
use app\common\events\PayLog;
use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\PayWithdrawOrder;
use app\common\services\finance\Withdraw;
use app\common\services\alipay\AopClient;
use app\common\services\alipay\request\AlipayFundTransToaccountTransferRequest;
use app\common\services\alipay\WebAlipay;
use app\common\services\Utils;

class SdkPayment
{

    private $__gateway_new = 'https://mapi.alipay.com/gateway.do?';

    private $__https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

    private $__http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';

    private $service = 'create_direct_pay_by_user';

    private $partner;

    private $_input_charset = 'UTF-8';

    private $sign_type = 'MD5';

    private $notify_url;

    private $return_url;

    private $out_trade_no;

    private $payment_type = 1;

    private $seller_id;

    private $total_fee;

    private $subject;

    private $body;

    private $it_b_pay;

    private $show_url;

    private $anti_phishing_key;

    private $exter_invoke_ip;

    private $key;

    private $transport;

    private $cacert;

    private $qr_pay_mode;

    private $paymethod = "bankPay";  //付款方式, 用于网银支付时为bankPay

    private $defaultbank;  //银行简码，用于网银支付

    private $payee_type = 'ALIPAY_LOGONID';

    public function __construct()
    {
        $this->cacert = getcwd() . '/../cacert.pem';
    }

    /**
     * 取得支付链接
     */
    public function getPayLink()
    {
        $parameter = array(
            'service' => $this->service,
            'partner' => $this->partner,
            'payment_type' => $this->payment_type,
            'notify_url' => $this->notify_url,
            'return_url' => $this->return_url,
            'seller_email' => $this->seller_id,
            'out_trade_no' => $this->out_trade_no,
            'subject' => $this->subject,
            'total_fee' => $this->total_fee,
            'body' => $this->body,
            'it_b_pay' => $this->it_b_pay,
            'show_url' => $this->show_url,
            'anti_phishing_key' => $this->anti_phishing_key,
            'exter_invoke_ip' => $this->exter_invoke_ip,
            '_input_charset' => strtolower($this->_input_charset),
            'qr_pay_mode' => $this->qr_pay_mode
        );
        //请求数据日志
        event(new PayLog($parameter, new WebAlipay()));

        $para = $this->buildRequestPara($parameter);

        return $this->__gateway_new . $this->createLinkstringUrlencode($para);
    }

    /**
     * 取得网银支付链接
     */
    public function getBankPayLink()
    {
        $parameter = array(
            'service' => $this->service,
            'partner' => $this->partner,
            'payment_type' => $this->payment_type,
            'notify_url' => $this->notify_url,
            'return_url' => $this->return_url,
            'seller_email' => $this->seller_id,
            'out_trade_no' => $this->out_trade_no,
            'subject' => $this->subject,
            'total_fee' => $this->total_fee,
            'body' => $this->body,
            'it_b_pay' => $this->it_b_pay,
            'show_url' => $this->show_url,
            'anti_phishing_key' => $this->anti_phishing_key,
            'exter_invoke_ip' => $this->exter_invoke_ip,
            '_input_charset' => strtolower($this->_input_charset),
            'qr_pay_mode' => $this->qr_pay_mode,

            // 网银支付额外配置
            'paymethod' => $this->paymethod,
            'defaultbank' => $this->defaultbank

        );

        $para = $this->buildRequestPara($parameter);

        return $this->__gateway_new . $this->createLinkstringUrlencode($para);
    }

    /**
     * 验证消息是否是支付宝发出的合法消息
     */
    public function verify()
    {
        // 判断请求是否为空
        if (empty($_POST) && empty($_GET)) {
            return false;
        }

        $data = $_POST ?: $_GET;

        // 生成签名结果
        $is_sign = $this->getSignVeryfy($data, $data['sign']);

        // 获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $response_txt = 'true';
        if (!empty($data['notify_id'])) {
            $response_txt = $this->getResponse($data['notify_id']);
        }

        // 验证
        // $response_txt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        // isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        if ($is_sign) {
            return true;
        } else {
            return false;
        }
    }

    public function setPayMethod($paymethod)
    {
        $this->paymethod = $paymethod;
        return $this;
    }

    public function getPayMethod()
    {
        return $this->paymethod;
    }

    public function setDefaultBank($bank)
    {
        $this->defaultbank = $bank;
        return $this;
    }

    public function setPartner($partner)
    {
        $this->partner = $partner;
        return $this;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function setNotifyUrl($notify_url)
    {
        $this->notify_url = $notify_url;
        return $this;
    }

    public function getNotifyUrl()
    {
        return $this->notify_url;
    }

    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;
        return $this;
    }

    public function getReturnUrl()
    {
        return $this->return_url;
    }

    public function setOutTradeNo($out_trade_no)
    {
        $this->out_trade_no = $out_trade_no;
        return $this;
    }

    public function getOutTradeNo()
    {
        return $this->out_trade_no;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function setSellerId($seller_id)
    {
        $this->seller_id = $seller_id;
        return $this;
    }

    public function getSellerId()
    {
        return $this->seller_id;
    }

    public function setTotalFee($total_fee)
    {
        $this->total_fee = $total_fee;
        return $this;
    }

    public function getTotalFee()
    {
        return $this->total_fee;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setItBPay($it_b_pay)
    {
        $this->it_b_pay = $it_b_pay;
        return $this;
    }

    public function getItBPay()
    {
        return $this->it_b_pay;
    }

    public function setShowUrl($show_url)
    {
        $this->show_url = $show_url;
        return $this;
    }

    public function setSignType($sign_type)
    {
        $this->sign_type = $sign_type;
        return $this;
    }

    public function setExterInvokeIp($exter_invoke_ip)
    {
        $this->exter_invoke_ip = $exter_invoke_ip;
        return $this;
    }

    public function getExterInvokeIp()
    {
        return $this->exter_invoke_ip;
    }

    public function setQrPayMode($qr_pay_mode)
    {
        $this->qr_pay_mode = $qr_pay_mode;
        return $this;
    }

    public function getQrPayMode()
    {
        $this->qr_pay_mode;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    private function buildRequestPara($para_temp)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);

        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);

        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper(trim($this->sign_type));

        return $para_sort;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    private function paraFilter($para)
    {
        $para_filter = array();
        while ((list ($key, $val) = each($para)) == true) {
            if ($key == 'sign' || $key == 'sign_type' || $val == '') {
                continue;
            } else {
                $para_filter[$key] = $para[$key];
            }
        }
        return $para_filter;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    private function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    private function buildRequestMysign($para_sort)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

        $mysign = '';
        switch (strtoupper(trim($this->sign_type))) {
            case 'MD5':
                $mysign = $this->md5Sign($prestr, $this->key);
                break;
            default:
                $mysign = '';
        }

        return $mysign;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private function createLinkstring($para)
    {
        $arg = '';
        while ((list ($key, $val) = each($para)) == true) {
            $arg .= $key . '=' . $val . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private function createLinkstringUrlencode($para)
    {
        $arg = '';
        while ((list ($key, $val) = each($para)) == true) {
            $arg .= $key . '=' . urlencode($val) . '&';
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    private function md5Sign($prestr, $key)
    {
        $prestr = $prestr . $key;
        return md5($prestr);
    }

    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    private function md5Verify($prestr, $sign, $key)
    {
        $prestr = $prestr . $key;

        $mysgin = md5($prestr);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    private function getSignVeryfy($para_temp, $sign)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

        $is_sgin = false;
        switch (strtoupper(trim($this->sign_type))) {
            case 'MD5':
                $is_sgin = $this->md5Verify($prestr, $sign, $this->key);
                break;
            default:
                $is_sgin = false;
        }

        return $is_sgin;
    }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    private function getResponse($notify_id)
    {
        $transport = strtolower(trim($this->transport));
        $partner = trim($this->partner);
        $veryfy_url = '';
        if ($transport == 'https') {
            $veryfy_url = $this->__https_verify_url;
        } else {
            $veryfy_url = $this->__http_verify_url;
        }
        $veryfy_url = $veryfy_url . 'partner=' . $partner . '&notify_id=' . $notify_id;
        $response_txt = $this->getHttpResponseGET($veryfy_url, $this->cacert);

        return $response_txt;
    }

    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    private function getHttpResponseGET($url, $cacert_url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); //SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $cacert_url); //证书地址
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

        return $responseText;
    }

    /**
     * 统一退款
     *
     * @return string
     */
    public function refund($out_refund_no)
    {
        $service = 'refund_fastpay_by_platform_pwd';
        $notify_url = Url::shopSchemeUrl('payment/alipay/refundNotifyUrl.php');

        $parameter = array(
            'service' => $service,
            'partner' => $this->partner,
            'seller_user_id' => $this->partner,
            'notify_url' => $notify_url,
            'seller_email' => $this->seller_id,
            'refund_date' => date('Y-m-d H:i:s', time()),
            'batch_no' => $out_refund_no,
            'batch_num' => 1,
            'detail_data' => $this->out_trade_no . '^' . $this->total_fee . '^退款订单',
            '_input_charset' => strtolower($this->_input_charset),
        );

        $para = $this->buildRequestPara($parameter);

        return ['url' => $this->__gateway_new . $this->createLinkstringUrlencode($para), 'batch_no' => $out_refund_no];
    }

    /**
     * 单次提现
     *
     * @param $collectioner_account
     * @param $collectioner_name
     * @return string
     */
    public function withdraw($collectioner_account, $collectioner_name, $out_trade_no, $batch_no)
    {
        $pay = Setting::get('shop.pay');
        Utils::dataDecrypt($pay);
\Log::debug('----提现类型----', [$pay['api_version']]);
        switch ($pay['api_version']) {
            case 1:
                return $this->withdraw_v1($pay, $collectioner_account, $collectioner_name, $out_trade_no, $batch_no);
                break;
            case 2:
                return $this->withdraw_v2($pay, $collectioner_account, $collectioner_name, $out_trade_no, $batch_no);
                break;
            default:
                return $this->withdraw_v1($pay, $collectioner_account, $collectioner_name, $out_trade_no, $batch_no);
        }
    }

    private function withdraw_v1($pay, $collectioner_account, $collectioner_name, $out_trade_no, $batch_no)
    {
        $service = 'batch_trans_notify';
        $notify_url = Url::shopSchemeUrl('payment/alipay/withdrawNotifyUrl.php');

        $parameter = array(
            'service' => $service,
            'partner' => $this->partner,
            'notify_url' => $notify_url,
            'email' => $pay['alipay_number'],
            'account_name' => $pay['alipay_name'],
            'pay_date' => date('Ymd', time()),
            'batch_no' => $batch_no,
            'batch_fee' => $this->total_fee,
            'batch_num' => 1,
            'detail_data' => $out_trade_no . '^' . $collectioner_account . '^' . $collectioner_name . '^' . $this->total_fee . '^佣金提现_' . \YunShop::app()->uniacid,
            '_input_charset' => strtolower($this->_input_charset),
        );

        $para = $this->buildRequestPara($parameter);

        return $this->__gateway_new . $this->createLinkstringUrlencode($para);
    }

    private function withdraw_v2($pay, $collectioner_account, $collectioner_name, $out_trade_no, $batch_no)
    {
        $res['errno'] = 1;
        $res['message'] = '系统异常,提现失败';

        $aop = new AopClient();
        $aop->appId = $pay['alipay_app_id'];
        $aop->rsaPrivateKey = $pay['rsa_private_key'];
        $aop->alipayrsaPublicKey= $pay['rsa_public_key'];
        $aop->signType = 'RSA2';

        $request = new AlipayFundTransToaccountTransferRequest();

        $data = [
            'out_biz_no' => $out_trade_no,
            'payee_type' => $this->payee_type,
            'payee_account' => $collectioner_account,
            'amount'     => $this->total_fee,
            'payer_show_name' => $pay['alipay_name'],
            'payee_real_name' => $collectioner_name,
            'remark' => '佣金提现'
        ];

        $request->setBizContent(json_encode($data));
        $result = $aop->execute ( $request);

        $result = json_decode($result);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        \Log::debug('-----返回参数code----', [$resultCode]);
        \Log::debug('-----返回参数sub_code----', [$result->$responseNode->sub_code]);

        if(!empty($resultCode) && $resultCode == 10000){
            \Log::debug('-----成功----');
            $out_biz_no = $result->$responseNode->out_biz_no;
            $data[] = [
                'trade_no' => $out_biz_no,
                'unit' => 'yuan',
                'pay_type' => '支付宝'
            ];

            $this->withdrawResutl($data);

            $res['errno'] = 0;
            $res['message']    = '提现成功';

            return $res;
        }

        \Log::debug('-----失败----');

        if (isset($result->$responseNode)) {
            throw new AppException($result->$responseNode->sub_msg . '[' . $result->$responseNode->msg . ']');
        }

        return $res;
    }

    /**
     * 批量打款
     *
     * @param $collectioner_account
     * @param $collectioner_name
     * @param $out_trade_no
     * @param $batch_no
     * @return string
     */
    public function batchWithdraw($collectioner_account, $collectioner_name, $withdraws, $batch_no)
    {
        $service = 'batch_trans_notify';
        $pay = Setting::get('shop.pay');
        Utils::dataDecrypt($pay);

        $notify_url = Url::shopSchemeUrl('payment/alipay/withdrawNotifyUrl.php');

        $total_fee   = 0;
        $detail_data = '';
        $batch_num    = 0;

        foreach ($withdraws as $key => $withdraw) {
            $total_fee += $withdraw->actual_amounts;
            $batch_num++;

            $detail_data .= $withdraw->withdraw_sn . '^' . $collectioner_account[$key] . '^' . $collectioner_name[$key] . '^' . $withdraw->actual_amounts . '^佣金提现_' . \YunShop::app()->uniacid . '|';
        }

        $parameter = array(
            'service' => $service,
            'partner' => $this->partner,
            'notify_url' => $notify_url,
            'email' => $pay['alipay_number'],
            'account_name' => $pay['alipay_name'],
            'pay_date' => date('Ymd', time()),
            'batch_no' => $batch_no,
            'batch_fee' => $total_fee,
            'batch_num' => $batch_num,
            'detail_data' => rtrim($detail_data, '|'),
            '_input_charset' => strtolower($this->_input_charset),
        );

        $para = $this->buildRequestPara($parameter);

        return $this->__gateway_new . $this->createLinkstringUrlencode($para);
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
                Withdraw::paySuccess($data['trade_no']);
                event(new AlipayWithdrawEvent($data['trade_no']));
            }
        }
    }
}
