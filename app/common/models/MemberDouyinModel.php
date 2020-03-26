<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/11/4
 * Time: 下午4:29
 */

namespace app\common\models;


/**
 * @property string $openid
 *
 * Class MemberMiniAppModel
 * @package app\common\models
 */
class MemberDouyinModel extends BaseModel
{
    public $table = 'yz_member_douyin';

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

    /**
     * 获取粉丝uid
     *
     * @param $openid
     * @return mixed
     */
    public static function getUId($openid)
    {
        return self::select('member_id')
            ->uniacid()
            ->where('openid', $openid)
            ->first();
    }

    /**
     * 删除会员信息
     *
     * @param $id
     */
    public static function  deleteMemberInfoById($id)
    {
        return self::uniacid()
            ->where('member_id', $id)
            ->delete();
    }
}