<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/25
 * Time: 下午5:34
 */

namespace app\frontend\modules\remittance\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Process;
use app\frontend\models\Order;
use app\frontend\models\RemittanceRecord;

class RemittanceRecordController extends ApiController
{

    /**
     * @throws AppException
     */
    public function index()
    {
        $orderId = request()->input('order_id');
        $order = Order::find($orderId);
        if (!isset($order)) {
            throw new AppException("未找到id为{$orderId}的订单记录");
        }
        /**
         * @var RemittanceRecord $remittanceRecord
         */
        $remittanceRecord = RemittanceRecord::where('order_pay_id', $order->order_pay_id)->orderBy('id', 'desc')->first();
        if (!isset($remittanceRecord)) {
            throw new AppException("未找到order_pay_id为{$order->order_pay_id}的转账记录");
        }
        $remittanceRecord->status_name = $remittanceRecord->currentProcess()->status_name;
        $remittanceRecord->audit_note = $remittanceRecord->currentProcess()->note ?: '';

        if ($remittanceRecord->currentProcess()->state == Process::STATUS_PROCESSING) {
            $remittanceRecord->button_models = [[
                "name" => "取消申请",
                "api" => "remittance.RemittanceRecordOperation.cancel",
                "value" => 1
            ]];
        }

        return $this->successJson('成功', $remittanceRecord);

    }

    public function upload()
    {
        $path = request()->file('file')->storeAs('remittanceRecord', str_random(10));
        return $this->successJson('上传成功', [
            'img'    => request()->getSchemeAndHttpHost(). config('app.webPath') . \Storage::url('app') .'/'. $path
        ]);
    }
}