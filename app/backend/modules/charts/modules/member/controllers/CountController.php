<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/10 上午10:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\backend\modules\charts\modules\member\services\CountService;
use app\common\components\BaseController;

class CountController extends BaseController
{
    protected $memberService;

    public function preAction()
    {
        parent::preAction();
        $this->memberService = new CountService();
    }

    public function index()
    {
        return view('charts.member.count',[
            'gender' => json_encode($this->memberService->getMemberSexStatistic()),
            'source' => json_encode($this->memberService->getMemberSourceStatistic()),
            'member_count' => $this->memberCount(),
        ])->render();
    }



    private function memberCount()
    {
        return [
            [
                'first_name'    => '会员总数',
                'second_name'   => '微信授权会员',
                'third_name'    => '微信授权会员（通过微信授权登录的会员）',
                'first_value'   => $this->memberService->memberCount,
                'second_value'  => $this->memberService->memberWechatCount,
                'third_value'   => $this->proportionMath($this->memberService->memberWechatCount)
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '绑定手机会员',
                'third_name'    => '绑定手机会员（包含手机号注册和微信绑定手机号的会员）',
                'first_value'   => $this->memberService->memberCount,
                'second_value'  => $this->memberService->memberHasMobileCount,
                'third_value'   => $this->proportionMath($this->memberService->memberHasMobileCount)
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '性别：男',
                'third_name'    => '所占比例',
                'first_value'   => $this->memberService->memberCount,
                'second_value'  => $this->memberService->memberManSexCount,
                'third_value'   => $this->proportionMath($this->memberService->memberManSexCount)
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '性别：女',
                'third_name'    => '所占比例',
                'first_value'   => $this->memberService->memberCount,
                'second_value'  => $this->memberService->memberFemaleSexCount,
                'third_value'   => $this->proportionMath($this->memberService->memberFemaleSexCount)
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '性别：未知',
                'third_name'    => '所占比例',
                'first_value'   => $this->memberService->memberCount,
                'second_value'  => $this->memberService->memberUnknownSexCount,
                'third_value'   => $this->proportionMath($this->memberService->memberUnknownSexCount)
            ]
        ];
    }

    private function proportionMath($divisor)
    {
        $member_count = $this->memberService->memberModel->where('uniacid', \YunShop::app()->uniacid)->count();
        $member_count = $member_count > 0 ? (int)$member_count : 1;

        return (bcdiv($divisor, $member_count,2) * 100) . "%";
    }

}
