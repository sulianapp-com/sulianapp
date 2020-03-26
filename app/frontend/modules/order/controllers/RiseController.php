<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/18
 * Time: 10:47
 */

namespace app\frontend\modules\order\controllers;


use app\common\models\order\Invoice;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Order;
class RiseController extends ApiController
{
   //获取发票图片
    public function getInvoice()
    {

        $db_remark_model = Order::select('invoice')->where('id', \YunShop::request()->order_id)->first();
        $invoice=yz_tomedia($db_remark_model->invoice);
        return $this->successJson('成功', ['invoice'=>$invoice]);

    }

}