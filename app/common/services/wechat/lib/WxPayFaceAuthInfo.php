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

class WxPayFaceAuthInfo extends WxPayDataBase
{
    public function __construct(WxPayConfig $config)
    {
        $this->values['appid'] = $config->GetAppId();
        $this->values['sub_appid'] = $config->GetSubAppId();
        $this->values["mch_id"] = $config->GetMerchantId();
        $this->values["sub_mch_id"] = $config->GetSubMerchantId();
        $this->values["version"] = "1";
        $this->values["sign_type"] = $config->GetSignType();
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

    public function SetStoreName($value)
    {
        $this->values['store_name'] = $value;
    }

    public function GetStoreName()
    {
        return $this->values['store_name'];
    }

    public function IsStoreNameSet()
    {
        return array_key_exists('store_name', $this->values);
    }

    public function SetStoreId($value)
    {
        $this->values['store_id'] = $value;
    }

    public function GetStoreId()
    {
        return $this->values['store_id'];
    }

    public function IsStoreIdSet()
    {
        return array_key_exists('store_id', $this->values);
    }

    public function SetDeviceId($value)
    {
        $this->values['device_id'] = $value;
    }

    public function GetDeviceId()
    {
        return $this->values['device_id'];
    }

    public function IsDeviceIdSet()
    {
        return array_key_exists('device_id', $this->values);
    }

    public function SetRawdata($value)
    {
        $this->values['rawdata'] = $value;
    }
    public function GetRawdata()
    {
        return $this->values['rawdata'];
    }
    public function IsRawdataSet()
    {
        return array_key_exists('rawdata', $this->values);
    }

    public function SetNow()
    {
        $this->values['now'] = time();
    }
    public function GetNow()
    {
        return $this->values['now'];
    }
    public function IsNowSet()
    {
        return array_key_exists('now', $this->values);
    }

    public function GetAuthInfo()
    {
        return $this->values['authinfo'];
    }

}