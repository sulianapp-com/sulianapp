<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/7 下午2:15
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\frontend\models\Member;

class PointPageController extends ApiController
{
    private $memberModel;


    public function index()
    {
        $this->getMemberInfo();
        if ($this->memberModel) {
            $result['credit1'] = $this->memberModel->credit1;
            $result['transfer'] = $this->getTransferStatus();
            $result['activity'] = $this->getActivityStatus();
            $result['rate'] = $this->getRateSet();


            return $this->successJson('ok',$result);
        }
        return $this->errorJson('未获取到会员信息');
    }

    private function getTransferStatus()
    {
        return Setting::get('point.set.point_transfer') ? true : false;
    }

    private function getActivityStatus()
    {
        return app('plugins')->isEnabled('point-activity');
    }

    private function getMemberInfo()
    {
        return $this->memberModel = Member::select('credit1')->where('uid',$this->getMemberId())->first();
    }

    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }

    private function getRateSet()
    {
        return intval(Setting::get('point.set.point_transfer_poundage'))/100 ?: 0;
    }
}
