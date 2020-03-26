<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-05-20
 * Time: 14:45
 */

namespace app\frontend\modules\finance\listeners;


use app\common\events\payment\RechargeComplatedEvent;
use app\common\exceptions\ShopException;
use app\common\models\finance\BalanceRecharge;
use app\framework\Support\Facades\Log;
use app\frontend\modules\finance\services\BalanceRechargeResultService;

class BalanceRechargeCompletedListener
{
    /**
     * 余额充值前缀
     *
     * @var string
     */
    private $prefix = "RV";

    /**
     * 充值金额
     *
     * @var double
     */
    private $amount;

    /**
     * yuan|fen
     *
     * @var string
     */
    private $unit;

    /**
     * 充值单号
     *
     * @var string
     */
    private $orderSn;

    /**
     * @var BalanceRecharge
     */
    private $rechargeModel;


    public function subscribe($events)
    {
        $events->listen(
            RechargeComplatedEvent::class, static::class . '@rechargeCompleted'
        );
    }

    /**
     * @param RechargeComplatedEvent $event
     * @throws ShopException
     */
    public function rechargeCompleted($event)
    {
        $resultData = $event->getRechargeData();

        $this->setUnit($resultData['unit']);
        $this->setRechargeAmount($resultData['total_fee']);
        $this->setRechargeOrderSn($resultData['order_sn']);

        if ($this->isHandle()) {
            $this->handleBalanceRechargeCompleted();
        }
    }


    /**
     * @param $unit
     */
    private function setUnit($unit)
    {
        $this->unit = (string)$unit;
    }

    /**
     * @param double $amount
     */
    private function setRechargeAmount($amount)
    {
        $this->amount = (double)$amount;
    }

    /**
     * @param string $orderSn
     */
    private function setRechargeOrderSn($orderSn)
    {
        $this->orderSn = (string)$orderSn;
    }

    /**
     * @return BalanceRecharge
     * @throws ShopException
     */
    private function setBalanceRechargeModel()
    {
        !isset($this->rechargeModel) && $this->_setBalanceRechargeModel();

        return $this->rechargeModel;
    }

    /**
     * @throws ShopException
     */
    private function _setBalanceRechargeModel()
    {
        $rechargeModel = BalanceRecharge::where('ordersn', $this->orderSn)->first();

        if (!$rechargeModel) {
            throw new ShopException('Balance recharge record do not exist！');
        }
        if ($rechargeModel->status == BalanceRecharge::PAY_STATUS_SUCCESS) {
            throw new ShopException('单号已经充值，不能重复充值（BALANCE）');
        }
        $this->rechargeModel = $rechargeModel;
    }

    /**
     * @return bool
     * @throws ShopException
     */
    private function isHandle()
    {
        return $this->isBelongBalanceRecharge() && $this->validatorRechargeAmount();
    }

    /**
     * @return bool
     */
    private function isBelongBalanceRecharge()
    {
        $prefix = strtoupper(substr($this->orderSn, 0, 2));

        return $prefix === $this->prefix;
    }

    /**
     * @return bool
     * @throws ShopException
     */
    private function validatorRechargeAmount()
    {
        $this->setBalanceRechargeModel();

        $completeAmount = $this->getCompletedAmount();

        $compare = bccomp($completeAmount, $this->rechargeModel->money, 2);
        if ($compare == 0) {
            return true;
        }
        $content = [
            'order_sn'  => $this->orderSn,
            'total_fee' => $this->amount
        ];
        Log::info('Balance recharge completed amount is error!', print_r($content, 1));
        $this->handleBalanceRechargeError();
        return false;
    }

    /**
     * @return float
     */
    private function getCompletedAmount()
    {
        if ($this->unit == 'fen') {
            return bcdiv($this->amount, 100, 2);
        }
        return $this->amount;
    }

    /**
     * 执行余额充值确认
     */
    private function handleBalanceRechargeCompleted()
    {
        (new BalanceRechargeResultService($this->rechargeModel))->confirm();
    }

    private function handleBalanceRechargeError()
    {
        $this->rechargeModel->remark = $this->rechargeModel->remark . ",确认支付金额【{$this->amount}元】";

        $this->rechargeModel->save();
    }
}
