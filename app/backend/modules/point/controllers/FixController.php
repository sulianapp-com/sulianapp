<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/16
 * Time: 3:11 PM
 */

namespace app\backend\modules\point\controllers;


use app\common\components\BaseController;
use app\common\models\finance\PointLog;
use app\common\models\Order;

class FixController extends BaseController
{
    public function index()
    {
        $orders = Order::
        where('create_time','>',strtotime('2018-10-25'))
            ->where('deduction_price','>',0)
            ->get();

        $error = $orders->filter(function (Order $order){

            $deduction_price = (int)$order->deduction_price;
            $p = PointLog::where("remark","订单[{$order->order_sn}]抵扣[{$deduction_price}]元")->count();
            return !$p;
        });
dd($error->pluck('order_sn')->implode(','));
    }
}