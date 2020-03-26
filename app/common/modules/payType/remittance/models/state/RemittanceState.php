<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */

namespace app\common\modules\payType\remittance\models\state;

use app\common\models\Order;
use app\common\models\Process;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;

class RemittanceState
{
    /**
     * @param Process $process
     * @return null
     */
    public function handle(Process $process)
    {
        /**
         * @var RemittanceProcess $remittanceProcess
         */
        $remittanceProcess = RemittanceProcess::find($process->id);

        // 转账流程结束后 解除订单锁定
        if ($process->state != Process::STATUS_PROCESSING) {
            $remittanceProcess->orderPay->orders->each(function (Order $order) {
                $order->is_pending = 0;
                $order->save();
            });
        }
    }

}