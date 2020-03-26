<?php

namespace app\frontend\modules\refund\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\RefundApply;
use app\frontend\models\Order;
use app\frontend\modules\refund\services\RefundService;
use app\frontend\modules\refund\services\RefundMessageService;
use Request;
use app\frontend\modules\order\services\MiniMessageService;
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 下午4:24
 */
class ApplyController extends ApiController
{
    public function index($request)
    {
        $this->validate([
            'order_id' => 'required|integer'
        ]);
        $order = Order::find($request->query('order_id'));
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }

        $reasons = [
            '不想要了',
            '卖家缺货',
            '拍错了/订单信息错误',
            '其他',
        ];
        $refundTypes = [];
        if ($order->status >= \app\common\models\Order::WAIT_SEND) {
            $refundTypes[] = [
                'name' => '退款(仅退款不退货)',
                'value' => 0
            ];
        }
        if ($order->status >= \app\common\models\Order::WAIT_RECEIVE) {

                $refundTypes[] = [
                    'name' => '退款退货',
                    'value' => 1
                ];
        }
        if ($order->status >= \app\common\models\Order::COMPLETE) {
            $refundTypes[] = [
                'name' => '换货',
                'value' => 2
            ];
        }

        $data = compact('order', 'refundTypes', 'reasons');
//        dd($data);
        return $this->successJson('成功', $data);
    }


    public function store($request)
    {

        $this->validate([
            'reason' => 'required|string',
            'content' => 'sometimes|string',
            'images' => 'sometimes|filled|json',
            'refund_type' => 'required|integer',
            'order_id' => 'required|integer'
        ], $request,[
            'reason.required'=>'退款原因未选择',
            'refund_type.required'=>'退款方式未选择',
            'images.json' => 'images非json格式'
        ]);

        $order = Order::find($request->input('order_id'));
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }
        if ($order->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('无效申请,该订单属于其他用户');
        }
        if ($order < Order::WAIT_SEND) {
            throw new AppException('订单未付款,无法退款');
        }

        if (Order::find($request->input('order_id'))->hasOneRefundApply) {
            throw new AppException('申请已提交,处理中');
        }

        $refundApply = new RefundApply($request->only(['reason', 'content', 'refund_type', 'order_id']));
        $refundApply->images = $request->input('images', []);
        $refundApply->content = $request->input('content', '');
        $refundApply->refund_sn = RefundService::createOrderRN();
        $refundApply->create_time = time();
        $refundApply->price = $order->price; 

        if (!$refundApply->save()) {
            throw new AppException('请求信息保存失败');
        }
        $order->refund_id = $refundApply->id;
        if (!$order->save()) {
            throw new AppException('订单退款状态改变失败');
        }

        //通知买家
        RefundMessageService::applyRefundNotice($refundApply);
        RefundMessageService::applyRefundNoticeBuyer($refundApply);
        return $this->successJson('成功', $refundApply->toArray());
    }
}