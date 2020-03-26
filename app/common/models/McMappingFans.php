<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/3/5
 * Time: 上午4:07
 */

namespace app\common\models;

use app\backend\models\BackendModel;

/**
 * Class McMappingFans
 * @package app\common\models
 */
class McMappingFans extends BackendModel
{
    public $table = 'mc_mapping_fans';

    protected $primaryKey = 'uid';

    public $timestamps = false;

//    public function getOauthUserInfo()
//    {
//        return mc_oauth_userinfo();
//    }
//
//    public function getMemberId($uniacid)
//    {
//        $user_info = $this->getOauthUserInfo();
//
//        return self::unionid()
//            ->where('openid', $user_info['openid'])
//            ->first()
//            ->toArray();
//    }

    public static function getUId($uniacid, $openid)
    {
        return self::select('uid')
            ->where('uniacid', $uniacid)
            ->where('openid', $openid)
            ->first();
    }

    public static function getFansById($memberId)
    {
        return self::uniacid()
            ->where('uid', $memberId)
            ->first();
    }

    public static function getAllFans()
    {
        return self::uniacid()
            ->where('uid', '>', 0)
            ->get();
    }

}