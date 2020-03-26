<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午5:24
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\member\controllers;


use app\common\components\ApiController;
use app\frontend\modules\member\models\MemberBankCard;
use app\frontend\models\MembershipInformationLog;

class BankCardController extends ApiController
{

    public function show()
    {
        $bankCard = MemberBankCard::where('member_id', $this->getMemberId())->first();

        !$bankCard && $bankCard = new MemberBankCard();

        $data = [
            'member_name' => $bankCard->member_name ?: "",
            'bank_card'   => $bankCard->bank_card ?: "",
            'bank_name'   => $bankCard->bank_name ?: "",
            'bank_province'   => $bankCard->bank_province ?: "",
            'bank_city'   => $bankCard->bank_city ?: "",
            'bank_branch'   => $bankCard->bank_branch ?: "",
        ];

        return $this->successJson('ok',$data);
    }


    public function edit()
    {
        $bankCard = MemberBankCard::where('member_id', $this->getMemberId())->first();

        !$bankCard && $bankCard = new MemberBankCard();

        $member_name = \YunShop::request()->member_name;
        $bank_card = \YunShop::request()->bank_card;
        $bank_name = \YunShop::request()->bank_name;
        $bank_province = \YunShop::request()->bank_province;
        $bank_city = \YunShop::request()->bank_city;
        $bank_branch = \YunShop::request()->bank_branch;

        $old_bankdata = [
            'member_name'       =>$bankCard->member_name,
            'bank_card'         =>$bankCard->bank_card,
            'bank_name'         =>$bankCard->bank_name,
            'bank_province'     =>$bankCard->bank_province,
            'bank_city'         =>$bankCard->bank_city,
            'bank_branch'       =>$bankCard->bank_branch
        ];

        $new_bankdata = [
            'member_name'       =>$member_name,
            'bank_card'         =>$bank_card,
            'bank_name'         =>$bank_name,
            'bank_province'     =>$bank_province,
            'bank_city'         =>$bank_city,
            'bank_branch'       =>$bank_branch
        ];

        $membership_infomation = [
            'uniacid'        => \YunShop::app()->uniacid,
            'uid'            => \YunShop::app()->getMemberId(),
            'old_data'       => serialize($old_bankdata),
            'new_data'       => serialize($new_bankdata),
            'session_id'     => session_id()
        ];
        $membership_infomation_log_model = MembershipInformationLog::create($membership_infomation);

        if ($bank_name && $bank_card && $member_name && $bank_province && $bank_city && $bank_branch) {
            //$post = json_decode($post);
            $bankCard->member_id = \YunShop::app()->getMemberId();
            $bankCard->member_name = $member_name;
            $bankCard->bank_card = $bank_card;
            $bankCard->bank_name = $bank_name;
            $bankCard->bank_province = $bank_province;
            $bankCard->bank_city = $bank_city;
            $bankCard->bank_branch = $bank_branch;
            $bankCard->is_default  = 1;
            $bankCard->uniacid     = \YunShop::app()->uniacid;


            $validator = $bankCard->validator();
            if ($validator->fails()) {
                return $this->errorJson($validator->messages()->first());
            }
            if (!$bankCard->save()) {
                return $this->errorJson('银行卡数据更新失败');
            }
            return $this->successJson('银行卡信息更新成功');
        }
        return $this->errorJson('未获取到银行卡数据');
    }



    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }


}
