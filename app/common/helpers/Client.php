<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/30
 * Time: 下午8:59
 */

namespace app\common\helpers;


class Client
{
    const DEVICE_MOBILE  = 1;
    const DEVICE_DESKTOP = 2;
    const DEVICE_UNKNOWN = -1;

    const BROWSER_TYPE_IPHONE  = 1;
    const BROWSER_TYPE_IPAD    = 2;
    const BROWSER_TYPE_IPOD	   = 3;
    const BROWSER_TYPE_ANDROID = 4;
    const BROWSER_TYPE_UNKNOWN = -1;

    const OS_TYPE_IOS	  = 1;
    const OS_TYPE_ANDROID = 2;
    const OS_TYPE_UNKNOWN = -1;

    const RETINA_TYPE_YES = 1;
    const RETINA_TYPE_NOT = 0;

    const IOS6_YES = 1;
    const IOS6_NOT = 0;

    const MICRO_MESSAGE_YES = 1;
    const MICRO_MESSAGE_NOT = 0;

    const APP_INSTALLED_YES = 1;
    const APP_INSTALLED_NOT = 0;

    public static function getDeviceInfo()
    {
        return array(
            'deviceType'  => self::deviceType(),
            'browserType' => self::browserType(),
            'isRetina' 	  => self::isRetina(),
            'osType' 	  => self::osType(),
            'isIos6' 	  => self::isIos6(),
        );
    }

    public static function browserType($agent = '')
    {
        $agent = self::getAgent($agent);

        if (stripos($agent, 'iphone') !== false) {
            return self::BROWSER_TYPE_IPHONE;
        }

        if (stripos($agent, 'ipad') !== false) {
            return self::BROWSER_TYPE_IPAD;
        }

        if (stripos($agent, 'ipod') !== false) {
            return self::BROWSER_TYPE_IPOD;
        }

        if (stripos($agent, 'android') !== false) {
            return self::BROWSER_TYPE_ANDROID;
        }

        return self::BROWSER_TYPE_UNKNOWN;
    }

    public static function osType($agent = '')
    {
        $agent = self::getAgent($agent);
        $browserType = self::browserType($agent);

        switch ($browserType) {
            case self::BROWSER_TYPE_IPHONE:
            case self::BROWSER_TYPE_IPAD:
            case self::BROWSER_TYPE_IPOD:
                $osType = self::OS_TYPE_IOS;
                break;
            case self::BROWSER_TYPE_ANDROID:
                $osType = self::OS_TYPE_ANDROID;
                break;
            default:
                $osType = self::OS_TYPE_UNKNOWN;
        }

        return $osType;
    }

    public static function deviceType()
    {
        if (self::isMobile()) {
            return self::DEVICE_MOBILE;
        } else {
            return self::DEVICE_DESKTOP;
        }
    }

    public static function isRetina($agent = '')
    {
        $agent = self::getAgent($agent);
        $osType = self::osType($agent);

        if (($osType == self::OS_TYPE_IOS) && (self::isIos6($agent) != 1)) {
            return self::RETINA_TYPE_YES;
        } else {
            return self::RETINA_TYPE_NOT;
        }
    }

    public static function isIos6($agent = '')
    {
        $agent = self::getAgent($agent);

        if (stripos($agent, 'iPhone OS 6')) {
            return self::IOS6_YES;
        } else {
            return self::IOS6_NOT;
        }
    }

    public static function isMicroMessage($agent = '')
    {
        $agent = self::getAgent($agent);

        if (stripos($agent, 'MicroMessenger') !== false) {
            return self::MICRO_MESSAGE_YES;
        } else {
            return self::MICRO_MESSAGE_NOT;
        }
    }

    public static function isAppInstalled()
    {
        if (isset($_GET['isappinstalled']) && ($_GET['isappinstalled'] == 1)) {
            return self::APP_INSTALLED_YES;
        } else {
            return self::APP_INSTALLED_NOT;
        }
    }

    public static function isMobile()
    {
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp',
                'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
                'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave',
                'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    public static function getAgent($agent = '')
    {
        $agent = empty($agent) ? $_SERVER['HTTP_USER_AGENT'] : $agent;
        return $agent;
    }

    public static function is_app()
    {
        if(defined('__MODULE_NAME__') && __MODULE_NAME__ == 'app/api'){
            return true;
        }
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $yunzhong = (strpos($agent, 'yunzshop')) ? true : false;
        if ($yunzhong) {
            return true;
        }

        return false;
    }

    static function is_weixin()
    {
        if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false) {
            return false;
        }
        return true;
    }
    public static function is_alipay()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'alipay') !== false && (app('plugins')->isEnabled('alipay-onekey-login'))) {
            return true;
        }
        return false;
    }

    /**
     * 获取随机字符串
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
     static function random($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    static function getType()
    {
        //微信浏览器
        if (self::is_weixin()) {
            return 1;
            //app浏览器
        } elseif (self::is_app()) {
            return 7;
        } elseif (self::is_alipay()) {
            return 8;
        }
        return 5;
    }

    public static function getOS()
    {
        switch (true) {
            case stristr(PHP_OS, 'DAR'): return 'OS_OSX';
            case stristr(PHP_OS, 'WIN'): return 'OS_WIN';
            case stristr(PHP_OS, 'LINUX'): return 'OS_LINUX';
            default : return self::OS_UNKNOWN;
        }
    }

    public static function is_nativeApp()
    {
        if(defined('__MODULE_NAME__') && __MODULE_NAME__ == 'app/api'){
            return true;
        }
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $yunzhong = (strpos($agent, 'yzshop')) ? true : false;
        if ($yunzhong) {
            return true;
        }

        return false;
    }

    public static function create_token($namespace = '')
    {
        $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['LOCAL_ADDR'];
        $data .= $_SERVER['LOCAL_PORT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));

        $token = substr($hash,  0,  8) . substr($hash,  8,  4) . substr($hash, 12,  4) .
            substr($hash, 16,  4) . substr($hash, 20, 12);

        return $token;
    }

    /**
     * 设置微信端手机号登录
     *
     * @param $type
     *
     * @return bool
     */
    public static function setWechatByMobileLogin($type)
    {
        if(1 == $type && \Setting::get('shop.member.wechat_login_mode') == 1){
            return true;
        }

        return false;
    }
}