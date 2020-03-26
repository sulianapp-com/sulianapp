<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/4/30
 * Time: 4:15 PM
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\frontend\modules\order\services\OrderService;

class ReceiveController extends BaseController
{
    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function index()
    {
        OrderService::orderReceive(request()->only(['order_id']));
        return $this->successJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function force()
    {
        $order = Order::find(request()->input('order_id'));
        $order->refund();
        return $this->successJson();
    }
}