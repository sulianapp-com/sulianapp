<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 上午11:59
 */

namespace app\frontend\modules\refund\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\RefundApply;

class EditController extends ApiController
{
    public function index(\Illuminate\Http\Request $request){
        $this->validate([
            'refund_id' => 'required|integer',
        ]);
        $reasons = [
            '不想要了',
            '卖家缺货',
            '拍错了/订单信息错误',
            '其他',
        ];
        $refundTypes = [
            [
                'name' => '退款(仅退款不退货)',
                'value' => 0
            ], [
                'name' => '退款退货',
                'value' => 1
            ], [
                'name' => '换货',
                'value' => 2
            ]
        ];
        $refundApply = RefundApply::find($request->query('refund_id'));
        if(!isset($refundApply)){
            throw new AppException('未找到该退款申请');
        }
        $data = compact('refundApply','reasons','refundTypes');
        return $this->successJson('成功',$data);
    }
    public function store(\Illuminate\Http\Request $request){
        $this->validate([
            'reason' => 'required|string',
            'content' => 'sometimes|string',
            'images' => 'sometimes|filled|json',
            'refund_type' => 'required|integer',
            'refund_id' => 'required|integer'
        ],request(), [
            'images.json' => 'images非json格式'
        ]);
        $refundApply = RefundApply::find($request->input('refund_id'));
        if (!isset($refundApply)) {
            throw new AppException('退款申请不存在');
        }
        if ($refundApply->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('无效申请,该订单属于其他用户');
        }

        $refundApply->fill($request->only(['reason', 'content', 'refund_type']));
        $refundApply->images = $request->input('images',[]);
        $refundApply->content = $request->input('content','');
        //$refundApply->price = $order->price;
        //$refundApply->create_time = time();
        if (!$refundApply->save()) {
            throw new AppException('请求失败');
        }

        return $this->successJson('成功', $refundApply->toArray());
    }
}