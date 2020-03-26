<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/2 下午2:12
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\services;

use app\frontend\models\MemberShopInfo;
use app\frontend\modules\member\models\MemberBankCard;

class WithdrawManualService
{

    public static function getWeChatStatus()
    {
        $yzMember = MemberShopInfo::select('wechat')->where('member_id',\YunShop::app()->getMemberId())->first();
        return $yzMember ? $yzMember->wechat ? true : false : false;
    }

    public static function getAlipayStatus()
    {
        $yzMember = MemberShopInfo::select('alipayname','alipay')->where('member_id',\YunShop::app()->getMemberId())->first();
        return $yzMember ? ($yzMember->alipayname && $yzMember->alipay) ? true : false : false;
    }


    public static function getBankStatus()
    {

        $bankCard = MemberBankCard::select('member_name','bank_card','bank_name','bank_province','bank_city','bank_branch')
            ->where('member_id', \YunShop::app()->getMemberId())->first();

        if ($bankCard->member_name &&
            $bankCard->bank_card &&
            $bankCard->bank_name &&
            $bankCard->bank_province &&
            $bankCard->bank_city &&
            $bankCard->bank_branch
        ) {
            return true;
        }
        return false;
    }
}

