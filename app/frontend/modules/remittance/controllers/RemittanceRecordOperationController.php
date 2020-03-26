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
use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;
use app\frontend\models\Order;
use app\frontend\models\RemittanceRecord;
use app\frontend\modules\process\controllers\Operate;

class RemittanceRecordOperationController extends ApiController
{
    use Operate;

    public $transactionActions = ['*'];

    protected function beforeStates()
    {
        return RemittanceAuditFlow::STATE_WAIT_AUDIT;
    }

    /**
     * @throws AppException
     */
    protected function _getProcess()
    {
        $orderId = request()->input('order_id');
        $order = Order::find($orderId);
        if(!isset($order)){
            throw new AppException("未找到id为{$orderId}的订单记录");
        }
        /**
         * @var RemittanceRecord $remittanceRecord
         */
        $remittanceRecord = RemittanceRecord::where('order_pay_id',$order->order_pay_id)->orderBy('id','desc')->first();
        if(!isset($remittanceRecord)){
            throw new AppException("未找到order_pay_id为{$order->order_pay_id}的转账记录");
        }
        return $remittanceRecord->currentProcess();
    }

    /**
     * @throws \Exception
     */
    public function cancel()
    {
        $this->toCancelState();
        return $this->successJson();
    }
}