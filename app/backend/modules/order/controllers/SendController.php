<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/27
 * Time: 下午4:30
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\models\Order;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\repositories\ExpressCompany;

class SendController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function index()
    {
        /**
         * @var Order $order
         */
        $order = Order::find(request('order_id'));
        if (!$order) {
            throw new AppException('未找到订单');
        }
        $expressCompanies = ExpressCompany::create()->all();
        return $this->successJson('成功', ['express_companies' => $expressCompanies, 'address' => $order->address]);
    }
}