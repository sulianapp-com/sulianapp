<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 下午8:12
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\order\Remark;
use app\common\models\Order;

class RemarkController extends BaseController
{
    public function index()
    {
        $order = Order::find(request()->input('order_id'));
        if(!$order){
            throw new AppException("未找到该订单".request()->input('order_id'));
        }
        if(request()->has('remark')){
           
            $remark = $order->hasOneOrderRemark;
            
            if (!$remark) {
                $remark = new Remark([
                    'order_id' => request()->input('order_id'),
                    'remark' => request()->input('remark')
                ]);

                if(!$remark->save()){
                    return $this->errorJson();
                }
           
                $order->invoice = request()->input('invoice');
                $order->save();
           
            } else {
                $reUp = Remark::where('order_id', request()->input('order_id') )
                    ->where('remark', $remark->remark)
                    ->update(['remark'=> request()->input('remark')]);

                if (!$reUp) {
                    return $this->errorJson();
                }
            }
        }
        //(new \app\common\services\operation\OrderLog($remark, 'special'));
        
        echo json_encode(["data" => '', "result" => 1]);
    }

}