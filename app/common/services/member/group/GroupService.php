<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 下午11:20
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\member\group;


use app\common\models\MemberGroup;

class GroupService
{
    public static function getMemberGroupList()
    {
        $groupList = MemberGroup::records()->orderBy('created_at')->get();
        return $groupList ? $groupList->toArray() : [];
    }

}
