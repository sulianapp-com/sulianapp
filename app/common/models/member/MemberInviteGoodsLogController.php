<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/14
 * Time: 20:12
 */

namespace app\common\models\member;


use app\common\models\BaseModel;

class MemberInviteGoodsLogController extends BaseModel
{
    public $table = 'yz_member_goods_invite_log';

    public static function getLogByMemberId($member_id)
    {
        return self::uniacid()->where('member_id', $member_id)->first();
    }
}