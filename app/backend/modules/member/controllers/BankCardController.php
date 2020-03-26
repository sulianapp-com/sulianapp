<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/23 下午2:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberBankCard;
use app\common\components\BaseController;
use app\common\helpers\Url;

class BankCardController extends BaseController
{

    public function edit()
    {
        $post = request()->input('bank');
        if ($post) {

            $_model = MemberBankCard::where('member_id', $this->getMemberId())->first();

            if ($_model) {
                $log = true;
            } else {
                $log = false;
            }
            !$_model && $_model = new MemberBankCard();

            $data = [
                'member_name' => $post['member_name'],
                'bank_card'   => $post['bank_card'],
                'bank_name'   => $post['bank_name'],
                'bank_province' => $post['bank_province'],
                'bank_city'     =>  $post['bank_city'],
                'bank_branch'   => $post['bank_branch'],
                'member_id'     => $this->getMemberId(),
                'is_default'    => 1,
                'uniacid'       => \YunShop::app()->uniacid,
            ];
//            $_model->member_name = $post['member_name'];
//            $_model->bank_card = $post['bank_card'];
//            $_model->bank_name = $post['bank_name'];
//            $_model->bank_province = $post['bank_province'];
//            $_model->bank_city = $post['bank_city'];
//            $_model->bank_branch = $post['bank_branch'];
//            $_model->member_id = $this->getMemberId();
//            $_model->is_default = 1;
//            $_model->uniacid = \YunShop::app()->uniacid;

            $_model->fill($data);
            $validator = $_model->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //dd($_model->save());
                if ($log) {
                    (new \app\common\services\operation\MemberBankCardLog($_model, 'update'));
                }
                if ($_model->save()) {

                    return $this->message('银行卡信息更新成功', Url::absoluteWeb('member.bank-card.edit', ['member_id' => $this->getMemberId()]));
                }
                return $this->message('银行卡信息更新失败，请重试', '', 'error');
            }

        }


        return view('member.bank.edit', ['member' => $this->getMemberModel()])->render();
    }

    private function getMemberModel()
    {
        return Member::select('uid', 'nickname', 'realname', 'mobile', 'avatar')
            ->with('bankCard')
            ->where('uid', $this->getMemberId())
            ->first();
    }

    private function getMemberId()
    {
        return trim(\YunShop::request()->member_id);
    }


}
