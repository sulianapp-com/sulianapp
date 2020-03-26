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

class PayController extends BaseController
{
    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function index()
    {
        $order = Order::find(request()->input('order_id'));
        $order->backendPay();
        return $this->successJson();
    }

}