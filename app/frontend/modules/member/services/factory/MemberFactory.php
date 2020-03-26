<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午2:27
 */

namespace app\frontend\modules\member\services\factory;

use app\frontend\modules\member\services\MemberAppYdbService;
use app\frontend\modules\member\services\MemberDouyinService;
use app\frontend\modules\member\services\MemberMiniAppFaceService;
use app\frontend\modules\member\services\MemberMobileService;
use app\frontend\modules\member\services\MemberNativeAppService;
use app\frontend\modules\member\services\MemberWechatService;
use app\frontend\modules\member\services\MemberAppWechatService;
use app\frontend\modules\member\services\MemberMiniAppService;
use app\frontend\modules\member\services\MemberOfficeAccountService;
use app\frontend\modules\member\services\MemberQQService;
use app\frontend\modules\member\services\MemberAlipayService;
use app\frontend\modules\member\services\SmsCodeService;

class MemberFactory
{
    const LOGIN_OFFICE_ACCOUNT = 1;
    const LOGIN_MINI_APP = 2;
    const LOGIN_APP_WECHAT = 3;
    const LOGIN_WECHAT = 4;
    const LOGIN_MOBILE = 5;
    const LOGIN_QQ = 6;
    const LOGIN_APP_YDB = 7;
    const LOGIN_ALIPAY = 8;
    const LOGIN_Native = 9;
    const LOGIN_MOBILE_CODE = 10;
    const LOGIN_DOUYIN = 11;
    const LOGIN_MINI_APP_FACE = 12;

    public static function create($type = null)
    {
        $className = null;

        switch($type)
        {
            case self::LOGIN_OFFICE_ACCOUNT:
                $className = new MemberOfficeAccountService();
                break;
            case self::LOGIN_MINI_APP:
                $className = new MemberMiniAppService();
                break;
            case self::LOGIN_APP_WECHAT:
                $className = new MemberAppWechatService();
                break;
            case self::LOGIN_WECHAT:
                $className = new MemberWechatService();
                break;
            case self::LOGIN_MOBILE:
                $className = new MemberMobileService();
                break;
            case self::LOGIN_QQ:
                $className = new MemberQQService();
                break;
            case self::LOGIN_APP_YDB:
                $className = new MemberAppYdbService();
                break;
            case self::LOGIN_ALIPAY:
                $className = new MemberAlipayService();
                break;
            case self::LOGIN_Native:
                $className = new MemberNativeAppService();
                break;
            case self::LOGIN_MOBILE_CODE:
                $className = new SmsCodeService();
                break;
            case self::LOGIN_DOUYIN:
                $className = new MemberDouyinService();
                break;
            case self::LOGIN_MINI_APP_FACE:
                $className = new MemberMiniAppFaceService();
                break;
            default:
                $className = null;
        }
        return $className;
    }
}