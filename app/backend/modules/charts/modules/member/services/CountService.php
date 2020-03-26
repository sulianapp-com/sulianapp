<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/12 下午12:10
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\services;


use app\backend\modules\charts\modules\member\models\Member;


class CountService
{
    public $memberModel;
    public $memberCount;
    public $memberManSexCount;
    public $memberFemaleSexCount;
    public $memberUnknownSexCount;
    public $memberWechatCount;
    public $memberHasMobileCount;

    public function __construct()
    {
        $this->memberModel = new Member();
        $this->getMemberCount();
        $this->getManSexCount();
        $this->getFemaleSexCount();
        $this->getUnknownSex();
        $this->getHasMobile();
        $this->getWechatAuthorizeCount();
    }

    public function getMemberSexStatistic()
    {
        return [
            [
                'name'  => '男',
                'value' => $this->memberManSexCount,
            ],
            [
                'name'  => '女',
                'value' => $this->memberFemaleSexCount,
            ],
            [
                'name'  => '未知',
                'value' => $this->memberUnknownSexCount,
            ]
        ];
    }


    public function getMemberSourceStatistic()
    {
        return [
            [
                'name'  => '微信授权',
                'value' => $this->memberWechatCount,
            ],
            [
                'name'  => '绑定手机',
                'value' => $this->memberHasMobileCount,
            ]
        ];
    }


    public function getManSexCount()
    {
        $this->memberManSexCount = $this->memberModel
            ->select('uid', 'mc_members.uniacid', 'gender', 'mobile')
            ->join('yz_member', function ($join) {
                $join->on('mc_members.uid', 'yz_member.member_id')
                    ->where('mc_members.gender', 1);
            })
            ->where('mc_members.uniacid', \YunShop::app()->uniacid)
            ->count();
    }

    public function getFemaleSexCount()
    {
        $this->memberFemaleSexCount = $this->memberModel
            ->select('uid', 'mc_members.uniacid', 'gender', 'mobile')
            ->join('yz_member', function ($join) {
                $join->on('mc_members.uid', 'yz_member.member_id')
                    ->where('mc_members.gender', 2);
            })
            ->where('mc_members.uniacid', \YunShop::app()->uniacid)
            ->count();
    }

    public function getUnknownSex()
    {
        $this->memberUnknownSexCount = $this->memberModel
            ->select('uid', 'mc_members.uniacid', 'gender', 'mobile')
            ->join('yz_member', function ($join) {
                $join->on('mc_members.uid', 'yz_member.member_id')
                    ->where('mc_members.gender', 0);
            })
            ->where('mc_members.uniacid', \YunShop::app()->uniacid)
            ->count();

        return $this->memberUnknownSexCount;
    }

    public function getHasMobile()
    {
        $this->memberHasMobileCount = $this->memberModel
            ->select('uid', 'mc_members.uniacid', 'gender', 'mobile')
            ->join('yz_member', function ($join) {
                $join->on('mc_members.uid', 'yz_member.member_id')
                    ->where('mc_members.mobile', '!=', '');
            })
            ->where('mc_members.uniacid', \YunShop::app()->uniacid)
            ->count();
    }

    public function getWechatAuthorizeCount()
    {
        $this->memberWechatCount = $this->memberModel
            ->select('uid', 'mc_members.uniacid', 'gender', 'mobile')
            ->join('mc_mapping_fans', function ($join) {
                $join->on('mc_members.uid', 'mc_mapping_fans.uid');
            })
            ->join('yz_member', function ($join) {
                $join->on('mc_members.uid', 'yz_member.member_id');
            })
            ->where('mc_members.uniacid', \YunShop::app()->uniacid)
            ->count();
    }

    public function getMemberCount()
    {
        $this->memberCount = $this->memberModel
            ->select('uid', 'mc_members.uniacid', 'gender', 'mobile')
            ->join('yz_member', function ($join) {
                $join->on('mc_members.uid', 'yz_member.member_id');
            })
            ->where('mc_members.uniacid', \YunShop::app()->uniacid)
            ->count();
    }
}
