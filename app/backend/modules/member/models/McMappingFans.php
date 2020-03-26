<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/24
 * Time: 下午4:33
 */

namespace app\backend\modules\member\models;


class McMappingFans extends \app\common\models\McMappingFans
{
    static protected $needLog = true;

    /**
     * 删除会员信息
     *
     * @param $id
     */
    public static function deleteMemberInfoById($id)
    {
        return self::uniacid()
            ->where('uid', $id)
            ->delete();
    }
}