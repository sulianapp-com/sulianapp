<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/8
 * Time: 16:41
 */

namespace app\common\services\wechat\lib;


use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayDataBase;

class WxPayOpenId extends WxPayDataBase
{
    public function __construct(WxPayConfig $config)
    {
        $this->values['appid'] = $config->GetAppId();
        $this->values["mch_id"] = $config->GetMerchantId();
        if ($config->GetSubAppId()) {
            $this->values['sub_appid'] = $config->GetSubAppId();
        }
        if ($config->GetSubMerchantId()) {
            $this->values["sub_mch_id"] = $config->GetSubMerchantId();
        }


    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return 值
     **/
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     **/
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }

    public function GetOpenid()
    {
        return $this->values['openid'];
    }

    public function GetSubOpenid()
    {
        return $this->values['sub_openid'];
    }


    /**
     * 设置扫码支付授权码，设备读取用户微信中的条码或者二维码信息
     * @param string $value
     **/
    public function SetAuth_code($value)
    {
        $this->values['auth_code'] = $value;
    }
    /**
     * 获取扫码支付授权码，设备读取用户微信中的条码或者二维码信息的值
     * @return 值
     **/
    public function GetAuth_code()
    {
        return $this->values['auth_code'];
    }
    /**
     * 判断扫码支付授权码，设备读取用户微信中的条码或者二维码信息是否存在
     * @return true 或 false
     **/
    public function IsAuth_codeSet()
    {
        return array_key_exists('auth_code', $this->values);
    }

}