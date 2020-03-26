<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/14
 * Time: 10:03
 */

namespace app\common\modules\yop\sdk;



class YopConfig
{
    public $serverRoot = "https://open.yeepay.com/yop-center";
    public $appKey;
    public $aesSecretKey;
    public $hmacSecretKey;
    public $connectTimeout=30000;
    public $readTimeout=60000;

    //加密算法
    public $ALG_MD5 = "MD5";
    public $ALG_AES = "AES";
    public $ALG_SHA = "SHA";
    public $ALG_SHA1 = "SHA1";


    // 保护参数
    public $CLIENT_VERSION = "2.0.0";
    public $ENCODING = "UTF-8";
    public $SUCCESS = "SUCCESS";
    public $CALLBACK = "callback";
    // 方法的默认参数名
    public $METHOD = "method";
    // 格式化默认参数名
    public $FORMAT = "format";
    // 本地化默认参数名
    public $LOCALE = "locale";
    // 会话id默认参数名
    public $SESSION_ID = "sessionId";
    // 应用键的默认参数名 ;
    public $APP_KEY = "appKey";
    // 服务版本号的默认参数名
    public $VERSION = "v";
    // 签名的默认参数名
    public $SIGN = "sign";
    // 返回结果是否签名
    public $SIGN_RETURN = "signRet";
    // 商户编号
    public $CUSTOMER_NO = "customerNo";
    // 加密报文key
    public $ENCRYPT = "encrypt";
    // 时间戳
    public $TIMESTAMP = "ts";
    public $publicED_KEY=array();


    public function __construct(){
        array_push($this->publicED_KEY,$this->APP_KEY, $this->VERSION, $this->SIGN, $this->METHOD, $this->FORMAT, $this->LOCALE, $this->SESSION_ID, $this->CUSTOMER_NO, $this->ENCRYPT, $this->SIGN_RETURN, $this->TIMESTAMP );
    }

    public function __set($name, $value){
        // TODO: Implement __set() method.
        $this->$name = $value;

    }
    public function __get($name){
        // TODO: Implement __get() method.
        return $this->$name;
    }


    public function getSecret(){

        if(!empty($this->appKey) && strlen($this->appKey) > 0){
            return $this->aesSecretKey;
        }else{
            return $this->hmacSecretKey;
        }
    }
    public function ispublicedKey($key){
        if(in_array($key,$this->publicED_KEY)){
            return true;
        }
        return false;

    }

}