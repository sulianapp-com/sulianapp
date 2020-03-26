<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:16
 */

namespace app\backend\modules\member\models;
/**
 * Class MemberShopInfo
 * @package app\backend\modules\member\models
 * @property MemberLevel level
 */
class MemberShopInfo extends \app\common\models\MemberShopInfo
{
    static protected $needLog = true;

    public function group()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberGroup');
    }

    public function level()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberLevel', 'level_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo('app\backend\modules\member\models\Member', 'parent_id', 'uid');
    }

    /**
     * 更新会员信息
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public static function updateMemberInfoById($data, $id)
    {
        return self::uniacid()
            ->where('member_id', $id)
            ->update($data);
    }

    /**
     * 清空会员表 的 yz_openid
     *
     * @param $id
     * @return mixed
     */
    public static function deleteMemberInfoOpenid($id)
    {
        return self::uniacid()->where('member_id', $id)->update(['yz_openid' => '0']);
    }

    /**
     * 删除会员信息
     *
     * @param $id
     * @return mixed
     */
    public static function deleteMemberInfo($id)
    {
        return self::uniacid()->where('member_id', $id)->delete();
    }

    /**
     * 设置会员黑名单
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public static function setMemberBlack($id, $data)
    {
        return self::uniacid()->where('member_id', $id)->update($data);
    }

    public static function getMemberLevel($memberId)
    {
        return self::uniacid()->select(['member_id', 'level_id'])->where('member_id', $memberId)
            ->with(['level' => function ($query) {
                return $query->select('id', 'level', 'level_name')->uniacid();
            }])->first();
    }

    public static function getParentOfMember($uid = [])
    {
        return self::uniacid()
            ->select(['member_id', 'parent_id'])
            ->whereIn('member_id', $uid)
            ->distinct()
            ->get();
    }

    public static function getParentOfMembeWithTrashed($uid = [])
    {
        return self::uniacid()
            ->withTrashed()
            ->select(['member_id', 'parent_id'])
            ->whereIN('member_id', $uid)
            ->distinct()
            ->get();
    }
}
