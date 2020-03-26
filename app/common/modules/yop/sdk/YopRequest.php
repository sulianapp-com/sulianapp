<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/14
 * Time: 10:01
 */

namespace app\common\modules\yop\sdk;


class YopRequest
{
    public $Config;

    public $format = 'json';
    public $method;
    public $locale = "zh_CN";
    public $version = "1.0";
    public $ImagePath = '';
    public $signAlg ;
    /**
     * 商户编号，易宝商户可不注册开放应用(获取appKey)也可直接调用API
     */
    public $customerNo;
    public $paramMap = array();
    public $ignoreSignParams = array('sign');
    /**
     * 报文是否加密，如果请求加密，则响应也加密，需做解密处理
     */
    public $encrypt = false;
    /**
     * 业务结果是否签名，默认不签名
     */
    public $signRet = false;
    /**
     * 连接超时时间
     */
    public $connectTimeout = 30000;
    /**
     * 读取返回结果超时
     */
    public $readTimeout = 60000;
    /**
     * 临时变量，避免多次判断
     */
    public $isRest = true;
    /**
     * 可支持不同请求使用不同的appKey及secretKey
     */
    public $appKey;
    /**
     * 可支持不同请求使用不同的appKey及secretKey,secretKey只用于本地签名，不会被提交
     */
    public $secretKey;
    /**
     * 可支持不同请求使用不同的appKey及secretKey、serverRoot,secretKey只用于本地签名，不会被提交
     */
    public $yopPublicKey;
    /**
     * 可支持不同请求使用不同的appKey及secretKey、serverRoot,secretKey只用于本地签名，不会被提交
     */
    public $serverRoot;
    /**
     * 临时变量，请求绝对路径
     */
    public $absoluteURL;
    public function __set($name, $value){
        // TODO: Implement __set() method.
        $this->$name = $value;

    }
    public function __get($name){
        // TODO: Implement __get() method.
        return $this->$name;
    }
    /**
     * 格式
     * @param string $format	设置格式:xml 或者 json
     */
    public function setFormat($format) {
        if(!empty($format)){
            $this->format = $format;

            $this->paramMap[$this->Config->FORMAT] = $this->format;
        }
    }
    public function setSignRet($signRet) {
        $signRetStr = $signRet?'true':'false';
        $this->signRet = $signRet;
        $this->addParam($this->Config->SIGN_RETURN, $signRetStr);
    }
    public function setEncrypt($encrypt) {
        $this->encrypt = $encrypt;
    }
    public function setSignAlg($signAlg) {
        $this->signAlg = $signAlg;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
        $this->paramMap[$this->Config->LOCALE] = $this->locale;
    }


    public function setVersion($version) {
        $this->version = $version;
        $this->paramMap[$this->Config->VERSION] = $this->version;

    }

    public function setMethod($method) {
        $this->method = $method;
        //$this->Config->METHOD = $this->method;
        $this->paramMap[$this->Config->METHOD] = $this->method;
    }


    public function __construct($appKey='', $secretKey,$serverRoot='',$yopPublicKey=null){ //定义构造函数
        $this->Config  = new YopConfig();
        $this->signAlg = $this->Config->ALG_SHA1;

        if(!empty($appKey)){
            $this->appKey = $appKey;
        }
        else{
            $this->appKey = $this->Config->appKey;
        }
        if(!empty($secretKey)){
            $this->secretKey = $secretKey;
        }
        else{
            $this->secretKey = $this->Config->getSecret();
        }
        if(!empty($yopPublicKey)){
            $this->yopPublicKey = $yopPublicKey;
        }
        else{
            $this->yopPublicKey = $this->Config->getSecret();
        }

        if(!empty($serverRoot)){
            $this->serverRoot = $serverRoot;
        }
        else{
            $this->serverRoot = $this->Config->serverRoot;
        }

        //初始化数组
        $this->paramMap[$this->Config->APP_KEY] = $this->appKey;
        //$this->paramMap[$this->Config->FORMAT] = $this->format;
        $this->paramMap[$this->Config->VERSION] = $this->version;
        $this->paramMap[$this->Config->LOCALE] = $this->locale;
//        $this->paramMap[$this->Config->TIMESTAMP] = 123456;
        $this->paramMap[$this->Config->TIMESTAMP] = time();


    }
    public function addParam($key,$values,$ignoreSign =false){
        if($ignoreSign){
            $addParam = array($key=>$values);
            $this->ignoreSignParams = array_merge($this->ignoreSignParams,$addParam);
        }
        $addParam = array($key=>$values);
        $this->paramMap = array_merge($this->paramMap,$addParam);
    }
    public function removeParam($key){
        foreach ($this->paramMap as $k => $v){
            if($key == $k){
                unset($this->paramMap[$k]);
            }
        }

    }
    public function getParam($key){
        return $this->paramMap[$key];
    }
    public function encoding(){
        foreach ($this->paramMap as $k=>$v){
            $this->paramMap[$k] = urlencode($v);
        }
    }

    /**
     * 将参数转换成k=v拼接的形式
     *
     *
     */
    public function toQueryString(){
        $StrQuery="";
        foreach ($this->paramMap as $k=>$v){
            $StrQuery .= strlen($StrQuery) == 0 ? "" : "&";
            $StrQuery.=$k."=".urlencode($v);
        }
        return $StrQuery;
    }

}