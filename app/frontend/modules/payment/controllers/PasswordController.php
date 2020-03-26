<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/22
 * Time: 下午3:12
 */

namespace app\frontend\modules\payment\controllers;


use app\common\components\ApiController;
use app\common\services\password\PasswordService;

class PasswordController extends ApiController
{
    public function check()
    {

        if (!\Setting::get('shop.pay.balance_pay_proving')) {
            // 未开启
            return true;
        }
        $this->validate([
            'password' => 'required|string'
        ]);
        (new PasswordService())->checkMemberPassword(\YunShop::app()->getMemberId(), request()->input('password'));
        return $this->successJson('成功', []);
    }
}