<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午10:23
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\services\PointService;
use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\helpers\Url;

class PointRechargeController extends BaseController
{
    public function index()
    {
        $member_id = \YunShop::request()->id;
        $result = (new PointService())->verifyPointRecharge(\YunShop::request()->point, Member::getMemberById($member_id));
        if ($result) {

            return $this->message($result, Url::absoluteWeb('finance.point-recharge', ['id' => $member_id]));
        }

        return view('finance.point.point_recharge', [
            'memberInfo'    => Member::getMemberInfoById($member_id),
            'rechargeMenu'  => $this->getRechargeMenu()
        ])->render();
    }

    private function getRechargeMenu()
    {
        return array(
            'title'     => '积分充值',
            'name'      => '粉丝',
            'profile'   => '会员信息',
            'old_value' => '当前积分',
            'charge_value' => '充值积分',
            'type'      => 'balance'
        );
    }
}