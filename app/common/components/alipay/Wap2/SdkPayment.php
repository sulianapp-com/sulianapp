<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/3
 * Time: 10:27 AM
 */

namespace app\common\components\alipay\Wap2;


use app\common\services\alipay\AopClient;
use app\common\services\alipay\request\AlipayTradeAppPayRequest;
use app\common\services\alipay\request\AlipayTradeWapPayRequest;

class SdkPayment
{
    /**
     * Request Mustf
     *
     * @var string
     */
    private $gateway_url = 'https://openapi.alipay.com/gateway.do';

    /**
     * Request Must
     *
     * @var string
     */
    private $app_id;

    /**
     * Request Must
     *
     * @var string
     */
    private $rsaPrivateKey;

    /**
     * Request Must
     *
     * @var string
     */
    private $alipayrsaPublicKey;

    /**
     * Request Must
     *
     * @var string
     */
    private $method;

    /**
     * Request Must
     *
     * @var string
     */
    private $charset = 'UTF-8';

    /**
     * Request Must
     *
     * @var string
     */
    private $sign_type = 'RSA2';

    /**
     * Request Must
     *
     * @var string
     */
    private $sign;

    /**
     * Request Must
     *
     * @var string
     */
    private $timestamp;

    /**
     * Request Must
     *
     * @var string
     */
    private $version = '1.0';

    /**
     * Request Must
     *
     * @var string
     */
    private $biz_content;

    /**
     * Request No
     *
     * @var string
     */
    private $format = 'JSON';

    /**
     * Request No
     *
     * @var string
     */
    private $return_url;

    /**
     * Request No
     *
     * @var string
     */
    private $notify_url;


    public function __construct()
    {

    }

    public function pageExecute($data)
    {
        $aopClient = new AopClient();

        $aopClient->gatewayUrl = $this->gateway_url;
        $aopClient->appId = $this->app_id;
        $aopClient->rsaPrivateKey = $this->rsaPrivateKey; //'请填写开发者私钥去头去尾去回车，一行字符串';
        $aopClient->alipayrsaPublicKey = $this->alipayrsaPublicKey; //'请填写支付宝公钥，一行字符串';
        $aopClient->apiVersion = $this->version;
        $aopClient->postCharset = $this->charset;
        $aopClient->format = $this->format;
        $aopClient->signType = $this->sign_type;

        $request = new AlipayTradeWapPayRequest();

        $request->setBizContent($data);
        $request->setNotifyUrl($this->notify_url);
        $request->setReturnUrl($this->return_url);

        $result = $aopClient->pageExecute($request, 'GET');
        \Log::info('-------test2-------', print_r($result,true));
        return $result;
    }

    public function sdkExecute($data)
    {
        $aopClient = new AopClient();

        $aopClient->gatewayUrl = $this->gateway_url;
        $aopClient->appId = $this->app_id;
        $aopClient->rsaPrivateKey = $this->rsaPrivateKey; //'请填写开发者私钥去头去尾去回车，一行字符串';
        $aopClient->alipayrsaPublicKey = $this->alipayrsaPublicKey; //'请填写支付宝公钥，一行字符串';
        $aopClient->apiVersion = $this->version;
        $aopClient->postCharset = $this->charset;
        $aopClient->format = $this->format;
        $aopClient->signType = $this->sign_type;

        $request = new AlipayTradeAppPayRequest();

        $request->setBizContent($data);
        $request->setNotifyUrl($this->notify_url);
        $request->setReturnUrl($this->return_url);
        $result = $aopClient->sdkExecute($request);
        \Log::info('-------test--tt-pay-------', print_r($result,true));
        return $result;
    }

    public function setAppId($app_id)
    {
        $this->app_id = $app_id;
        return $this;
    }

    public function setRsaPrivateKey($rsaPrivateKey)
    {
        $this->rsaPrivateKey = $rsaPrivateKey;
        return $this;
    }

    public function setAlipayrsaPublicKey($alipayrsaPublicKey)
    {
        $this->alipayrsaPublicKey = $alipayrsaPublicKey;
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    public function setSignType($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setSign($sign)
    {
        $this->sign = $sign;
        return $this;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function setBizContent($biz_content)
    {
        $this->biz_content = $biz_content;
        return $this;
    }

    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;
        return $this;
    }

    public function setNotifyUrl($notify_url)
    {
        $this->notify_url = $notify_url;
        return $this;
    }

}
