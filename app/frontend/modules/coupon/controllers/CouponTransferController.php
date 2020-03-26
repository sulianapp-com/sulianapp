<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/26 下午1:44
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\coupon\controllers;


use app\common\components\ApiController;
use app\frontend\models\Member;
use app\frontend\modules\coupon\models\MemberCoupon;
use app\frontend\modules\coupon\services\CouponSendService;
use app\backend\modules\coupon\services\MessageNotice;

class CouponTransferController extends ApiController
{
    public $memberModel;

    public function index()
    {
        $recipient = trim(\YunShop::request()->recipient);
        if (!$this->getMemberInfo()) {
            return  $this->errorJson('未获取到会员信息');
        }
        if (!Member::select('uid')->where('uid',$recipient)->first()) {
            return  $this->errorJson('被转让者不存在');
        }
        if ($this->memberModel->uid == $recipient) {
            return  $this->errorJson('转让者不能是自己');
        }


        $record_id = trim(\YunShop::request()->record_id);
        $_model = MemberCoupon::select('id','coupon_id')->where('id',$record_id)->first();
        if (!$_model) {
            return $this->errorJson('未获取到该优惠券记录ID');
        }

        $couponService = new CouponSendService();
        $result = $couponService->sendCouponsToMember($recipient,[$_model->coupon_id],'5','',$this->memberModel->uid);
        if (!$result) {
            return $this->errorJson('转让失败：(写入出错)');
        }

        $result = MemberCoupon::where('id',$_model->id)->update(['used' => 1,'use_time' => time(),'deleted_at' => time()]);
        if (!$result) {
            return $this->errorJson('转让失败：(记录修改出错)');
        }
//        '.$this->memberModel->uid.''.[$_model->coupon_id].'

        //发送获取通知
        MessageNotice::couponNotice($_model->coupon_id,$recipient);

        return $this->successJson('转让成功,');
    }


    private function getMemberInfo()
    {
        return $this->memberModel = Member::select('uid')->where('uid',\YunShop::app()->getMemberId())->first();
    }






}
