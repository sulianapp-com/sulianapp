<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\PayType;
use app\frontend\modules\order\services\OrderService;
use app\common\models\order\Remark;
use app\common\exceptions\AppException;

class OperationController extends BaseController
{
    protected $param;
    /**
     * @var Order
     */
    protected $order;
    public $transactionActions = ['*'];

    public function preAction()
    {
        parent::preAction();

        $this->param = request()->input();

        if (!isset($this->param['order_id'])) {
            return $this->message('order_id不能为空!', '', 'error');

        }
        $this->order = Order::find($this->param['order_id']);
        if (!isset($this->order)) {
            return $this->message('未找到该订单!', '', 'error');

        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function pay()
    {
        $this->order->backendPay();
        return $this->successJson();

    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function cancelPay()
    {
        OrderService::orderCancelPay($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function send()
    {
        OrderService::orderSend($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function fClose(){
        $this->order->refund();
        return $this->message('强制退款成功');

    }
    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function cancelSend()
    {
        OrderService::orderCancelSend($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function receive()
    {
        OrderService::orderReceive($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function close()
    {
        OrderService::orderClose($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function manualRefund()
    {

        $result = $this->order->refund();

        if (isset($result['url'])) {
            return redirect($result['url'])->send();
        }

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function delete()
    {
        OrderService::orderDelete($this->param);

        return $this->message('操作成功');
    }

    public function remarks()
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

    public function invoice()
    {
        $order = Order::find(request()->input('order_id'));
        
        if(!$order){
            throw new AppException("未找到该订单".request()->input('order_id'));
        }

        if (request()->has('invoice')) {
            $order->invoice = request()->input('invoice');
            $order->save();
        }
        echo json_encode(["data" => '', "result" => 1]);
    }
}