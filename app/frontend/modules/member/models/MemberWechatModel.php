<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午10:43
 */

namespace app\frontend\modules\member\models;

use app\backend\models\BackendModel;

class MemberWechatModel extends BackendModel
{
    public $table = 'yz_member_wechat';

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

    public static function getUserInfoByUuid($uuid)
    {
        return self::uniacid()
            ->where('uuid', $uuid)
            ->first();
    }

    public static function getUserInfo_memberid($member_id)
    {
        return self::uniacid()
            ->where('member_id', $member_id)
            ->first();
    }

    public static function updateUserInfo($openid, $data)
    {
        return self::uniacid()
            ->where('openid', $openid)
            ->update($data);
    }

    public static function updateData($member_id, $data)
    {
        self::uniacid()
            ->where('member_id', $member_id)
            ->update($data);
    }

    /**
     * 获取用户信息
     *
     * @param $memberId
     * @return mixed
     */
    public static function getFansById($memberId)
    {
        return self::uniacid()
            ->where('member_id', $memberId)
            ->first();
    }
}