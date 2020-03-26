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
use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;
use app\common\modules\process\ProcessStatus;
use app\common\modules\status\StatusObserver;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\payType\remittance\PreRemittanceRecord;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;

class RemittanceStatus
{
    /**
     * @var RemittanceProcess
     */
    private $process;
    /**
     * @param Process $process
     * @return null
     */
    public function handle(Process $process)
    {
        if (!method_exists($this, $process->status->code)) {
            return null;
        }
        $this->process = RemittanceProcess::find($process->id);
        return $this->{$process->status->code}();


    }

    /**
     * 待收款
     * @throws \Exception
     */
    private function waitReceipt(){

        $this->process->orderPay->orders->each(function (Order $order) {
            $order->pay_type_id = PayType::REMITTANCE;
            $order->order_pay_id = $this->process->orderPay->id;
            $order->is_pending = 1;
            $order->save();
        });
        // todo  验证参数是否存在
        $transferRecord = new PreRemittanceRecord(
            [
                'report_url' => request()->input('report_url', ''),
                'note' => request()->input('note', ''),
                'uid' => $this->process->orderPay->uid,
                'order_pay_id' => $this->process->model_id,
                'card_no' => request()->input('card_no', ''),
                'amount' => request()->input('amount', 0),
                'bank_name' => request()->input('bank_name', ''),
            ]
        );
        $transferRecord->save();

        $transferRecord->addProcess(RemittanceAuditFlow::first());
    }
}