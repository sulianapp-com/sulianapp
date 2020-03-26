<?php

/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/13
 * Time: 16:29
 */

namespace app\common\modules\yop\sdk;

use app\common\modules\yop\sdk\YopRequest;
use app\common\modules\yop\sdk\YopResponse;
use app\common\modules\yop\sdk\Util\YopSignUtils;
use app\common\modules\yop\sdk\Util\HttpRequest;
use app\common\modules\yop\sdk\Util\BlowfishEncrypter;
use app\common\modules\yop\sdk\Util\AESEncrypter;

class YopClient{

    public function __construct(){

    }
    public function __set($name, $value){
        // TODO: Implement __set() method.
        $this->$name = $value;

    }
    public function __get($name){
        // TODO: Implement __get() method.
        return $this->$name;
    }
    static public function get($methodOrUri, $YopRequest){
        $content = YopClient::getForString($methodOrUri, $YopRequest);
        $response = YopClient::unmarshal($content);
        YopClient::handleResult($YopRequest , $response, $content);
        return $response;

    }
    static public function getForString($methodOrUri, $YopRequest){
        $serverUrl = YopClient::richRequest($methodOrUri, $YopRequest);
        YopClient::signAndEncrypt($YopRequest);
        $YopRequest->absoluteURL = $serverUrl;
        $YopRequest->encoding();
        $serverUrl .= (strpos($serverUrl,'?') === false ?'?':'&') . $YopRequest->toQueryString();
        $response = YopClient::getRestTemplate($serverUrl,$YopRequest,"GET");
        return $response;
    }
    static public function post($methodOrUri, $YopRequest){
        $content = YopClient::postForString($methodOrUri, $YopRequest);
        $response = YopClient::unmarshal($content);
        YopClient::handleResult($YopRequest , $response, $content);
        return $response;
    }

    static public function postForString($methodOrUri, $YopRequest){
        $serverUrl = YopClient::richRequest($methodOrUri, $YopRequest);
        YopClient::signAndEncrypt($YopRequest);
        $YopRequest->absoluteURL = $serverUrl;
        $response = YopClient::getRestTemplate($serverUrl,$YopRequest,"POST");
        return $response;
    }
    static public function upload($methodOrUri, $YopRequest){

        $content = YopClient::uploadForString($methodOrUri, $YopRequest);
        $response = YopClient::unmarshal($content);
        YopClient::handleResult($YopRequest , $response, $content);
        return $response;
    }
    static public function uploadForString($methodOrUri, $YopRequest){



        $serverUrl = YopClient::richRequest($methodOrUri, $YopRequest);

        //$alternate = file_get_contents($YopRequest->getParam("_file"));

        YopClient::signAndEncrypt($YopRequest);

        //$YopRequest->addParam("_file",str_replace('file:','@',$strTemp));PUT
        // Create a CURLFile object
        //$cfile = curl_file_create($file);

        //echo $YopRequest->getParam("_file");


        $YopRequest->absoluteURL = $serverUrl;

        $response = YopClient::getRestTemplate($serverUrl,$YopRequest,"PUT");
        return $response;
    }



    static public function unmarshal($jsoncontent){
        /*
         *
            {
              "state" : "FAILURE",
              "ts" : 1469523373843,
              "error" : {
                "code" : "U000001",
                "message" : "会员不存在"
             }
         * */

        $YopResponse =new YopResponse();
        if(!empty($jsoncontent['state'])){
            $YopResponse->state = $jsoncontent['state'];
        }
        if(!empty($jsoncontent['error'])){
            if(is_array($jsoncontent['error'])){
                foreach ($jsoncontent['error'] as $k => $v) {
                    if(!is_array($v)){
                        $YopResponse->error .= (empty($YopResponse->error)?'':',') . '"'. $k .'" : "'.$v.'"';
                    }else{
                        $YopResponse->error .= (empty($YopResponse->error)?'':',') . '"'. $k .'" : "'.json_encode($v,JSON_UNESCAPED_UNICODE).'"';
                        foreach ($v as $vk=>$vv){

                        }
                    }

                }

            }else{
                $YopResponse->error = $jsoncontent['error'];
            }

        }
        if(!empty($jsoncontent['result'])){
            $YopResponse->result = $jsoncontent['result'];
        }
        if(!empty($jsoncontent['ts'])){
            $YopResponse->ts = $jsoncontent['ts'];
        }
        if(!empty($jsoncontent['sign'])){
            $YopResponse->sign = $jsoncontent['sign'];
        }
        if(!empty($jsoncontent['stringResult'])){
            $YopResponse->stringResult = $jsoncontent['stringResult'];
        }
        if(!empty($jsoncontent['format'])){
            $YopResponse->format = $jsoncontent['format'];
        }
        if(!empty($jsoncontent['validSign'])){
            $YopResponse->validSign = $jsoncontent['validSign'];
        }

        return $YopResponse;
    }

    static public function getRestTemplate($serverUrl, $YopRequest,$method){
        $YopRequest->encoding();

        if($method == "GET"){
            return HTTPRequest::curl_request($serverUrl, '', $YopRequest->Config->connectTimeout,true);
        }elseif ($method == "PUT"){

            //$YopRequest->addParam("_file",$YopRequest->ImagePath,true );


            return HTTPRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout,true,true);
        }


        return HTTPRequest::curl_request($serverUrl, $YopRequest->paramMap, $YopRequest->Config->connectTimeout,true);
    }

    static public function signAndEncrypt($YopRequest){



        if(empty($YopRequest->method)){
            error_log("method must be specified");
        }
        if(empty($YopRequest->secretKey)){
            error_log("secretKey must be specified");
        }
        $appKey =$YopRequest->{$YopRequest->Config->APP_KEY};
        if(empty($appKey)){
            $appKey = $YopRequest->Config->CUSTOMER_NO;
            $YopRequest->removeParam($YopRequest->Config->APP_KEY);
        }
        if(empty($appKey)){
            error_log("appKey 与 customerNo 不能同时为空");
        }

        $signValue="";
        $signValue=YopSignUtils::sign($YopRequest->paramMap,$YopRequest->ignoreSignParams,$YopRequest->secretKey,$YopRequest->signAlg);



        $YopRequest->addParam($YopRequest->Config->SIGN,$signValue);
        if($YopRequest->isRest){
            $YopRequest->removeParam($YopRequest->Config->METHOD);
            $YopRequest->removeParam($YopRequest->Config->VERSION);
        }
        if($YopRequest->encrypt){
            YopClient::encrypt($YopRequest);
        }

    }
    static public function encrypt($YopRequest){
        $builder = $YopRequest->paramMap;
        foreach ($builder as $k => $v){
            if($YopRequest->Config->ispublicedKey($k)){
                unset($builder[$k]);
            }else{
                //$YopRequest->removeParam($k);
            }
        }
        if(!empty($builder)){
            $encryptBody="";
            foreach ($builder as $k=>$v){
                $encryptBody .= strlen($encryptBody) == 0 ? "" : "&";
                $encryptBody .= $k."=".urlencode($v);
            }
        }
        if(empty($encryptBody)){
            $YopRequest->addParam($YopRequest->Config->ENCRYPT,true);

        }else{
            if(!empty($YopRequest->{$YopRequest->Config->APP_KEY})){

                $encrypt = AESEncrypter::encode($encryptBody,$YopRequest->secretKey);
                $YopRequest->addParam($YopRequest->Config->ENCRYPT,$encrypt);
            }else{
                $encrypt = BlowfishEncrypter::encode($encryptBody,$YopRequest->secretKey);
                $YopRequest->addParam($YopRequest->Config->ENCRYPT,$encrypt);
            }

        }

    }
    static public function decrypt($YopRequest, $strResult){
        if(!empty($strResult) && $YopRequest->{$YopRequest->Config->ENCRYPT}){
            if(!empty($YopRequest->{$YopRequest->Config->APP_KEY})){
                $strResult = AESEncrypter::decode($strResult, $YopRequest->secretKey);
            }else{
                $strResult = BlowfishEncrypter::decode($strResult, $YopRequest->secretKey);
            }
        }
        return  $strResult;
    }

    static public function richRequest( $methodOrUri, $YopRequest){

        if(strpos($methodOrUri, $YopRequest->Config->serverRoot)){
            $methodOrUri = substr($methodOrUri,strlen($YopRequest->Config->serverRoot)+1);
        }
        $isRest = (strpos($methodOrUri,"/rest/") == 0)?true:false;
        $YopRequest->isRest = $isRest;
        $serverUrl = $YopRequest->serverRoot;
        if($isRest){
            $serverUrl .= $methodOrUri;
            preg_match('@/rest/v([^/]+)/@i', $methodOrUri, $version);
            if(!empty($version)){
                $version = $version[1];
                if(!empty($version)){
                    $YopRequest->setVersion($version);
                }
            }


        }else{
            $serverUrl .=  "/command?" . $YopRequest->Config->METHOD . "=" . $methodOrUri;
        }
        $YopRequest->setMethod($methodOrUri);
        return $serverUrl;
    }

    public static function handleResult($YopRequest, $YopResponse, $content){
        $YopResponse->format = $YopRequest->format;
        $ziped='';
        if(strtoupper($YopResponse->state) == 'SUCCESS'){
            $strResult = self::getBizResult($content,$YopRequest->format);
            $ziped = str_replace(PHP_EOL, '', $strResult);
            if(!empty($ziped) && $YopResponse->error == ''){
                if($YopRequest->encrypt){
                    $decryptResult = self::decrypt($YopRequest,trim($ziped));
                    $YopResponse->stringResult = $decryptResult;
                    $YopResponse->result = $decryptResult;
                    $ziped =str_replace(PHP_EOL, '', $decryptResult);

                }else{
                    $YopResponse->stringResult = $strResult;
                }
            }
        }

        if($YopRequest->signRet && !empty($YopResponse->sign)){
            $signStr = $YopResponse->state . $ziped .  $YopResponse->ts;

            $signStr= preg_replace("/[\s]{2,}/","",$signStr);

            $signStr= str_replace(PHP_EOL,"",$signStr);

            $signStr= str_replace(" ","",$signStr);

            $YopResponse->validSign = YopSignUtils::isValidResult($signStr, $YopRequest->secretKey, $YopRequest->signAlg,$YopResponse->sign);

        }else{
            $YopResponse->validSign = true;
        }

    }
    private static function getBizResult($content, $format){
        if(empty($format)){
            return $content;
        }
        switch ($format){
            case 'json':
                //preg_match('@"result" :(.+),"ts"@i', $content, $jsonStr);
                return $content['result'];
            default:
                //preg_match('@</state>(.+)<ts>@i', $content, $xmlStr);
                return $content['result'];
        }
    }


}
