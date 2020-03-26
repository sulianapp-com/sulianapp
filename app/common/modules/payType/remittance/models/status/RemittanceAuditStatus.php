<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */

namespace app\common\modules\payType\remittance\models\status;

use app\common\models\Order;
use app\common\models\PayType;
use app\common\models\Process;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;

class RemittanceAuditStatus
{
    /**
     * @var RemittanceAuditProcess
     */
    private $process;

    public function handle(Process $process)
    {
        if (!method_exists($this, $process->status->code)) {
            return null;
        }

        $this->process = RemittanceAuditProcess::find($process->id);

        return $this->{$process->status->code}();
    }

    /**
     * 取消
     * @throws \Exception
     */
    private function canceled()
    {
        /**
         * @var RemittanceAuditProcess $process
         */
        // 转账流程->下一步
        $this->process->remittanceRecord->orderPay->currentProcess()->toCancelStatus();
        // 支付记录->支付
        $this->process->remittanceRecord->orderPay->orders->each(function (Order $order) {
            $order->pay_type_id = 0;
            $order->order_pay_id = '';
            $order->save();
        });
    }

    /**
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    private function passed()
    {
        /**
         * @var RemittanceAuditProcess $process
         */
        // 转账流程->下一步
        $this->process->remittanceRecord->orderPay->currentProcess()->toNextStatus();
        // 支付记录->支付
        $this->process->remittanceRecord->orderPay->pay(PayType::REMITTANCE);
    }

    /**
     * @throws \Exception
     */
    private function refused()
    {
        /**
         * @var RemittanceAuditProcess $process
         */
        // 转账流程->下一步
        $this->process->remittanceRecord->orderPay->currentProcess()->toCloseStatus();

        $this->process->note = request()->input('note', '');
        $this->process->save();
    }
}