<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/8/7
 * Time: 17:42
 */

namespace app\frontend\modules\payment\orderPayments;


use Yunshop\TeamRewards\common\models\TeamRewardsMemberModel;

class DepositPayment extends BasePayment
{
    public function canUse()
    {
        return parent::canUse() && $this->depositEnough();
    }

    private function depositEnough()
    {
        if (!app('plugins')->isEnabled('team-rewards')) {
            return false;
        }
        $set = \Setting::get('team-rewards.is_open');
        if($set != 1)
        {
            return false;
        }
        $memberId = \YunShop::app()->getMemberId();
        $pluginMember = TeamRewardsMemberModel::uniacid()->where('member_id',$memberId)->first();
        if($pluginMember && $pluginMember->deposit >= $this->orderPay->amount)
        {
            return true;
        }
        return false;
    }
}