<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/5/23
 * Time: 上午11:17
 */

namespace app\frontend\modules\dispatch\discount;

use app\frontend\models\MemberShopInfo;

/**
 * 会员等级运费优惠
 * Class EnoughReduce
 * @package app\frontend\modules\dispatch\discount
 */
class  LevelFreeFreight extends BaseFreightDiscount
{
    protected $name = '会员等级运费优惠';
    protected $code = 'LevelFreeFreight';
    protected function _getAmount()
    {
        $uid = intval($this->order->belongsToMember->uid);
        $member = MemberShopInfo::select('level_id')->with('level')->find($uid);

        if (isset($member->level) && isset($member->level->freight_reduction)) {
            $freight_reduction = intval($member->level->freight_reduction);

            return ($this->order->getDispatchAmount() * ($freight_reduction / 100));
        }
        return 0;
    }
}