<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 19/11/4
 * Time: 上午10:44
 */

/**
 * 字节跳动小程序登录表
 */

namespace app\frontend\modules\member\models;

class MemberDouyinModel extends \app\common\models\MemberDouyinModel
{
    public static function insertData($data)
    {
        self::insert($data);
    }

    public static function getUserInfo($openid)
    {
        return self::uniacid()
            ->where('openid', $openid)
            ->first();
    }

    public static function updateUserInfo($openid, $data)
    {
        return self::uniacid()
            ->where('openid', $openid)
            ->update($data);
    }

    public static function updateData($uid, $data)
    {
        self::uniacid()
            ->where('member_id', $uid)
            ->update($data);
    }

    public static function getMemberByToken($token)
    {
        return self::uniacid()
            ->where('access_token', $token)
            ->first();
    }
}