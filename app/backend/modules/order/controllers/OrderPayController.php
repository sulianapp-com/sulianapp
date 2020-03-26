<?php
/**
 * 订单详情
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/4
 * Time: 上午11:16
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\models\Order;
use app\common\components\BaseController;

class OrderPayController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $orderId = request()->query('order_id');
        $order = Order::with(['orderPays' => function ($query) {
            $query->with('orders');
        }])->find($orderId);

        return view('order.orderPay', [
            'orderPays' => json_encode($order->orderPays)
        ])->render();
    }

}