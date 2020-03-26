<?php
/**
 * Created by PhpStorm.
 * User: xudong.ding
 * Date: 16/5/18
 * Time: 下午2:09
 */
namespace app\common\services\alipay\f2fpay\model\builder;

class AlipayAuthTokenContentBuilder extends ContentBuilder
{
    private $bizContent = NULL;
    private $appAuthToken;
    private $grantType;
    private $code;
    private $refreshToken;

    private $bizParas = array();


    public function __construct()
    {
    }

    public function AlipayTradePayContentBuilder()
    {
        $this->__construct();
    }

    public function getBizContent()
    {
        /*$this->bizContent = "{";
        foreach ($this->bizParas as $k=>$v){
            $this->bizContent.= "\"".$k."\":\"".$v."\",";
        }
        $this->bizContent = substr($this->bizContent,0,-1);
        $this->bizContent.= "}";*/
        if(!empty($this->bizParas)){
            $this->bizContent = json_encode($this->bizParas,JSON_UNESCAPED_UNICODE);
        }

        return $this->bizContent;
    }

    public function getAppAuthToken()
    {
        return $this->appAuthToken;
    }

    public function setAppAuthToken($appAuthToken)
    {
        $this->appAuthToken = $appAuthToken;
        $this->bizParas['app_auth_token'] = $appAuthToken;
    }

    public function getGrantType()
    {
        return $this->grantType;
    }

    public function setGrantType($grantType)
    {
        $this->grantType = $grantType;
        $this->bizParas['grant_type'] = $grantType;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
        $this->bizParas['code'] = $code;
    }
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
        $this->bizParas['refresh_token'] = $refreshToken;
    }

}

?>