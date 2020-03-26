<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:38
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\Order;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\VerifyPayService;

class TestPayController extends BaseController
{

    public function index()
    {
        //$param =  ['order_no' => $_POST['order_no'], 'amount' => $_POST['amount'], 'subject' => $_POST['subject'], 'body' => $_POST['body'], 'extra' => $_POST['extra']];
        $param = [
            'order_no' => time(),
            'amount' => 0.1,
            'subject' => '微信支付',
            'body' => '商品的描述:2',
            'extra' => ['type'=>1]
        ];
        $pay = PayFactory::create(PayFactory::PAY_WEACHAT);
        $data = $pay->doPay($param);
        return $this->successJson('成功',$data);
    }
    public function test(){
        define('IS_TEST',true);
        $param = [
            'order_no' => time(),
            'amount' => 0.1,
            'subject' => '微信支付',
            'body' => '商品的描述:2',
            'extra' => ''
        ];
        $pay = PayFactory::create(PayFactory::PAY_WEACHAT);
        $data = $pay->doPay($param);
        dump($data);exit;
    }
}