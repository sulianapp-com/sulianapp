<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/16 上午9:59
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\models;


use app\backend\modules\member\models\MemberShopInfo;

class YzMember extends MemberShopInfo
{

    /**
     * 通过 $level（$level 为 N 级下线， 1 为1级下线，2为二级下线，3为三级下线） 获取会员下级 ids 集合，
     * @param $memberId 【会员ID】
     * @param string $level 【1，2，3】
     * @return mixed
     */
    /* public static function getMemberOffline($memberId,$level = '')
     {
         $array      = $level ? [$memberId,$level] : [$memberId];
         $condition  = $level ? ' = ?' : '';
         return static::select('member_id')->whereRaw('FIND_IN_SET(?,relation)' . $condition, $array)->get();
     }*/

}
