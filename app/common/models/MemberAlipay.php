<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/13
 * Time: 14:40
 */

namespace app\common\models;


class MemberAlipay extends BaseModel
{
    public $table = 'yz_member_alipay';
    protected $primaryKey = 'alipay_id';

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