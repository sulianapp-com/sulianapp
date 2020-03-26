<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/8/25 上午9:27
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services;


use app\backend\modules\goods\models\DivFrom;
use app\common\models\OrderGoods;
use app\frontend\models\Member;

class DivFromService
{
    public static function isDisplay(array $goodsIds,$memberId = '')
    {
        $result = false;
        $goodsDivFrom = DivFrom::whereIn('goods_id',$goodsIds)->get();
        if ($goodsDivFrom) {
            foreach ($goodsDivFrom as $key => $goods) {
                if ($goods['status']) {
                    $result = true;
                    break;
                }
            }
        }
        //$result = !$result ? $result : static::getMemberStatus($memberId);
        return $result;
    }

    public static function getMemberStatus($memberId)
    {
        $member_info = static::getMemberCardAndName($memberId);
        if ($member_info['realname'] && $member_info['idcard']) {
            return true;
        }
        return false;
    }

    public static function getMemberCardAndName($memberId)
    {
        return $member_info = Member::select('realname','idcard')->where('uid',$memberId)->first();
    }

}
