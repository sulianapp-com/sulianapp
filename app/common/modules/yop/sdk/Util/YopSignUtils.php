<?php

namespace app\common\modules\yop\sdk\Util;

use app\common\modules\yop\sdk\Util\Base64Url;
use app\common\modules\yop\sdk\Util\AESEncrypter;

/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/7
 * Time: 17:33
 */
abstract class YopSignUtils{

    /**
     * 签名生成算法
     * @param array $params API调用的请求参数集合的关联数组，不包含sign参数
     * @param array $ignoreParamNames 忽略的参数数组
     * @param String $secret 密钥
     * @param String $algName 加密算法
     *
    md2
    md4
    md5
    sha1
    sha256
    sha384
    sha512
    ripemd128
    ripemd160
    ripemd256
    ripemd320
    whirlpool
     *
     * @return string 返回参数签名值
     */
    static function sign($params, $ignoreParamNames='', $secret, $algName='sha1'){
        $str = '';  //待签名字符串
        //先将参数以其参数名的字典序升序进行排序
        $requestparams = $params;

        ksort($requestparams);
        //遍历排序后的参数数组中的每一个key/value对
        foreach ($requestparams as $k => $v) {
            //查看Key 是否为忽略参数
            if(!in_array($k,$ignoreParamNames)){
                //为key/value对生成一个keyvalue格式的字符串，并拼接到待签名字符串后面

                //value不为空,则进行加密
                if(!empty($v)){
                    $str .= "$k$v";
                }

            }

        }
        //将签名密钥拼接到签名字符串两头
        $str = $secret.$str.$secret;
        //通过指定算法生成sing

        return hash($algName,$str);
    }


    /**
     * 签名验证算法
     * @param array $result API调用的请求参数集合的关联数组，不包含sign参数
     * @param String $secret 密钥
     * @param String $algName 加密算法
     * @param String $sign 签名值
     * @return string 返回签名是否正确 0 - 如果两个字符串相等
     */
   static function isValidResult($result, $secret, $algName,$sign){
       $newString = $secret.$result.$secret;

       if(strcasecmp($sign,hash($algName,$newString))==0){
           return true;
       }else{
           return false;
       }

   }
   
   static function verifySign($source,$sign, $public_Key)
   {
       $content=strstr($source, '&sign', TRUE);
       $public_key = "-----BEGIN PUBLIC KEY-----\n" .
           wordwrap($public_Key, 64, "\n", true) .
           "\n-----END PUBLIC KEY-----";

       $publicKey = openssl_pkey_get_public($public_key);
       $res = openssl_verify($content,Base64Url::decode($sign), $public_key,'SHA256'); //验证
 
       openssl_free_key($publicKey);
       //输出验证结果，1：验证成功，0：验证失败
       if ($res == 1) {
           return true;
       } else {
           Die("verifySign fail!");
       }
   }

   static function decrypt($source,$private_Key, $public_Key)
   {

       $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
           wordwrap($private_Key, 64, "\n", true) .
           "\n-----END RSA PRIVATE KEY-----";

       extension_loaded('openssl') or die('php需要openssl扩展支持');


       /* 提取私钥 */
       $privateKey = openssl_get_privatekey($private_key);

       ($privateKey) or die('密钥不可用');


       //分解参数
       $args = explode('$', $source);


       if (count($args) != 4) {
           die('source invalid : ');
       }

       $encryptedRandomKeyToBase64 = $args[0];
       $encryptedDataToBase64 = $args[1];
       $symmetricEncryptAlg = $args[2];
       $digestAlg = $args[3];

       //用私钥对随机密钥进行解密
       openssl_private_decrypt(Base64Url::decode($encryptedRandomKeyToBase64), $randomKey, $privateKey);


       openssl_free_key($privateKey);




       $encryptedData = openssl_decrypt(Base64Url::decode($encryptedDataToBase64), "AES-128-ECB", $randomKey, OPENSSL_RAW_DATA);


       //分解参数
       $signToBase64=substr(strrchr($encryptedData,'$'),1);
       $sourceData = substr($encryptedData,0,strlen($encryptedData)-strlen($signToBase64)-1);

       $public_key = "-----BEGIN PUBLIC KEY-----\n" .
           wordwrap($public_Key, 64, "\n", true) .
           "\n-----END PUBLIC KEY-----";



       $publicKey = openssl_pkey_get_public($public_key);

       $res = openssl_verify($sourceData,Base64Url::decode($signToBase64), $publicKey,$digestAlg); //验证

       openssl_free_key($publicKey);

       //输出验证结果，1：验证成功，0：验证失败
       if ($res == 1) {
           return $sourceData;
       } else {
           Die("verifySign fail!");
       }
   }

    static function signRsa($source,$private_Key)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_Key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        extension_loaded('openssl') or die('php需要openssl扩展支持');


        /* 提取私钥 */
        $privateKey = openssl_get_privatekey($private_key);

        ($privateKey) or die('密钥不可用');

        openssl_sign($source, $encode_data, $privateKey, "SHA256");

        openssl_free_key($privateKey);

        $signToBase64 = Base64Url::encode($encode_data);


        $signToBase64 .= '$SHA256';


        return $signToBase64;

    }

   /* static function getPrivateKey($filepath,$password)
    {
        $pkcs12 = file_get_contents($filepath);
        openssl_pkcs12_read($pkcs12, $certs, $password);
        $prikeyid = $certs['pkey']; //私钥

        $prikeyid = str_replace('-----BEGIN RSA PRIVATE KEY-----','',$prikeyid);
        $prikeyid = str_replace('-----END RSA PRIVATE KEY-----','',$prikeyid);

        $prikeyid = preg_replace("/(\r\n|\n|\r|\t)/i", '', $prikeyid);

        return $prikeyid;

    }*/

}

