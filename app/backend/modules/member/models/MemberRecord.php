<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/10/24
 * Time: 下午9:48
 */

namespace app\backend\modules\member\models;


use app\common\models\BaseModel;

class MemberRecord extends BaseModel
{
    protected $table = 'yz_member_record';

    public static function getRecord($uid)
    {
        return self::uniacid()
            ->where('uid', $uid)
            ->orderBy('id', 'desc')
            ->get();
    }
}