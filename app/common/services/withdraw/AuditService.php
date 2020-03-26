<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/20 下午2:33
 * Email: livsyitian@163.com
 */

namespace app\common\services\withdraw;


use app\common\events\withdraw\WithdrawAuditedEvent;
use app\common\events\withdraw\WithdrawAuditEvent;
use app\common\events\withdraw\WithdrawAuditingEvent;
use app\common\exceptions\ShopException;
use app\common\models\Income;
use app\common\models\Withdraw;
use Illuminate\Support\Facades\DB;
use app\common\services\finance\BalanceNoticeService;
use app\common\services\finance\MessageService;

class AuditService
{
    /**
     * @var Withdraw
     */
    private $withdrawModel;

    /**
     * @var float
     */
    private $audit_amount;

    private $set;

    public function __construct(Withdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
        $this->setAuditAmount();

        $this->set = \Setting::get('withdraw.income');
    }


    /**
     * 提现审核接口
     *
     * @return bool
     * @throws ShopException
     */
    public function withdrawAudit()
    {
        if ($this->withdrawModel->status == Withdraw::STATUS_INITIAL || $this->withdrawModel->status == Withdraw::STATUS_INVALID) {
            try {

                return $this->_withdrawAudit();

            } catch (\Exception $e) {

                $this->sendMessage();
            }
        }
        throw new ShopException("提现审核：ID{$this->withdrawModel->id}，不符合审核规则");
    }


    /**
     * @return bool
     */
    private function _withdrawAudit()
    {
        DB::transaction(function () {
            $this->audit();
        });
        return true;
    }


    private function audit()
    {
        event(new WithdrawAuditEvent($this->withdrawModel));

        $this->auditing();
    }


    private function auditing()
    {
        $this->withdrawModel->status = $this->getAuditStatus();
        $this->withdrawModel->audit_at = time();
        $this->withdrawModel->actual_poundage = $this->getActualPoundage();
        $this->withdrawModel->actual_servicetax = $this->getActualServiceTax();
        $this->withdrawModel->actual_amounts = $this->getActualAmount();

        event(new WithdrawAuditingEvent($this->withdrawModel));

        $this->audited();
    }


    private function getAuditStatus()
    {
        $type_ids_count = count(array_filter(explode(',', $this->withdrawModel->type_id)));

        //$audit_count = count($this->withdrawModel->audit_ids);
        $rebut_count = count($this->withdrawModel->rebut_ids);
        $invalid_count = count($this->withdrawModel->invalid_ids);

        //如果全无效
        if ($invalid_count > 0 && $invalid_count == $type_ids_count) {
            return Withdraw::STATUS_INVALID;
        }

        //如果全驳回
        if ($rebut_count > 0 && $rebut_count == $type_ids_count) {
            return Withdraw::STATUS_REBUT;
        }

        //如果是无效 + 驳回 [同全驳回，直接完成]
        if ($invalid_count > 0 && $rebut_count > 0 && ($invalid_count + $rebut_count) == $type_ids_count) {
            return Withdraw::STATUS_PAY;
        }
        return Withdraw::STATUS_AUDIT;
    }


    /**
     * @throws ShopException
     */
    private function audited()
    {
        $validator = $this->withdrawModel->validator();
        if ($validator->fails()) {

            throw new ShopException($validator->messages()->first());
        }

        if (!$this->withdrawModel->save()) {

            throw new ShopException("提现审核：ID{$this->withdrawModel->id}，记录更新失败");
        }
        event(new WithdrawAuditedEvent($this->withdrawModel));
    }


    /**
     * 审核后最终手续费
     *
     * @return float
     */
    private function getActualPoundage()
    {
        $amount = $this->audit_amount;
        $rate = $this->withdrawModel->poundage_rate;
        if ($this->withdrawModel->poundage_type == 1) {
            if ($amount != 0) {
                return $rate;
            } else {
                return 0;
            }

        }

        return bcdiv(bcmul($amount, $rate, 4), 100, 2);
    }

    /**
     * 审核后最终劳务税
     * @return string
     * @throws ShopException
     */
    private function getActualServiceTax()
    {
        $withdraw_set = \Setting::get('withdraw.income');

        $audit_amount = $this->audit_amount;   //收入总和
        if (!$withdraw_set['service_tax_calculation']) {
            $poundage = $this->getActualPoundage(); //手续费
            $audit_amount = bcsub($audit_amount, $poundage, 2); //收入总和减去手续费
        }

        if ($audit_amount < 0 && $audit_amount != 0) {

            throw new ShopException("驳回部分后提现金额小于手续费，不能通过申请！");
        }

        //计算劳务税
//       $rate = $this->withdrawModel->servicetax_rate;

        $rate = $this->getLastActualServiceTax($audit_amount, $withdraw_set);
        $this->withdrawModel->servicetax_rate = $rate;

        return bcdiv(bcmul($audit_amount, $rate, 4), 100, 2);
    }

    /**
     * 审核后最终金额
     *
     * @return float
     */
    private function getActualAmount()
    {
        $amount = $this->audit_amount;
        $poundage = $this->getActualPoundage();
        $service_tax = $this->getActualServiceTax();

        return bcsub(bcsub($amount, $poundage, 2), $service_tax, 2);
    }


    private function setAuditAmount()
    {
        !isset($this->audit_amount) && $this->audit_amount = $this->auditIncomeAmount();
    }


    /**
     * 审核通过的收入金额和
     *
     * @return float
     */
    private function auditIncomeAmount()
    {
        $audit_ids = $this->withdrawModel->audit_ids;

        $amount = Income::uniacid()->whereIn('id', $audit_ids)->sum('amount');

        return $this->audit_amount = $amount;
    }

    /**
     * 增加劳务税梯度
     * @param $amount
     * @return mixed
     */
    private function getLastActualServiceTax($amount, $withdraw_set)
    {
        $servicetax_rate = $withdraw_set['servicetax_rate'];
        if ($this->withdrawModel->servicetax_rate != $servicetax_rate) {
            return $this->withdrawModel->servicetax_rate;
        }
        $servicetax = $withdraw_set['servicetax'];
        if (empty($servicetax)) {
            return $servicetax_rate;
        }

        $max_money = array_column($servicetax, 'servicetax_money');
        array_multisort($max_money, SORT_DESC, $servicetax);

        foreach ($servicetax as $value) {
            if ($amount >= $value['servicetax_money'] && !empty($value['servicetax_money'])) {
                return $value['servicetax_rate'];
                break;
            }
        }
        return $servicetax_rate;
    }

    private function sendMessage()
    {
        if ($this->set['free_audit'] == 1) {
            if ($this->withdrawModel->type == 'balance') {
                //余额提现失败通知
                BalanceNoticeService::withdrawFailureNotice($this->withdrawModel);
            } else {
                $ids = \Setting::get('withdraw.notice.withdraw_user');
                foreach ($ids as $k => $v) {
                    (new MessageService($this->withdrawModel))->failureNotice($v['uid']);
                }
            }
        }
    }
}
