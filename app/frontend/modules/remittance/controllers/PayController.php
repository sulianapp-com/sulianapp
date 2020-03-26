<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/16
 * Time: 上午11:10
 */

namespace app\frontend\modules\remittance\controllers;


use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\models\Process;
use app\common\modules\payType\remittance\models\flows\RemittanceFlow;
use app\frontend\models\OrderPay;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;
use app\frontend\modules\process\controllers\Operate;

class PayController extends BaseController
{
    use Operate;
    public $transactionActions = ['*'];
    /**
     * @var RemittanceProcess
     */
    protected $process;
    protected $name = '确认支付';

    /**
     * @return Process
     * @throws AppException
     */
    protected function _getProcess()
    {
        $orderPayId = request()->input('order_pay_id');
        /**
         * @var OrderPay $orderPay
         */
        $orderPay = OrderPay::find($orderPayId);
        if (!isset($orderPay)) {
            throw new AppException("未找到该支付记录(id:{$orderPayId})");
        }
        $process = $orderPay->currentProcess();
        if (!isset($process)) {
            throw new AppException("未找到该流程(order_pay_id:{$orderPayId})");
        }
        return $process;
    }

    /**
     * @return string
     */
    protected function beforeStates()
    {
        return RemittanceFlow::STATE_WAIT_REMITTANCE;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        $this->validate([
            'report_url' => 'required',
            'amount' => 'numeric',
        ]);
        $this->toNextState();
        return $this->successJson();
    }
}