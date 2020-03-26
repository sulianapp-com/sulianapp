<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 下午10:06
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\member\level;


use app\common\models\MemberLevel;

class LevelService
{
    public static function getMemberLevelList()
    {
        $memberList = MemberLevel::records()->orderBy('level')->get();
        return $memberList ? $memberList->toArray() : [];
    }

}
