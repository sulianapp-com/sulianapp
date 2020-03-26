<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/12
 * Time: 下午4:51
 */

namespace app\backend\modules\order\controllers;


use app\backend\modules\order\models\Order;
use app\common\components\BaseController;

class JobController extends BaseController
{
    public function index(){
        $order = Order::find(request('id'));
        dump($order->orderCreatedJob->toArray());
        dump($order->orderPaidJob->toArray());
        dump($order->orderSentJob->toArray());
        dump($order->orderReceivedJob->toArray());
    }
}