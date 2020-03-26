<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/7/11
 * Time: 下午 06:09
 */

namespace app\frontend\modules\withdraw\listeners;


use app\common\events\withdraw\WithdrawBalanceAppliedEvent;
use app\Jobs\WithdrawBalanceAuditFreeJob;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\facades\Setting;

class WithdrawBalanceApplyListener
{
    public function subscribe(Dispatcher $dispatcher)
    {
        /**
         * 2019-11-01 应丁冉要求，无效提现免审核相关功能
         * 提现申请后，免审核任务
         */
//        $dispatcher->listen(
//            WithdrawBalanceAppliedEvent::class,
//            static::class . "@withdrawBalanceApplied",
//            999
//        );
    }

    /**
     * 提现申请后，免审核任务
     *
     * @param $event WithdrawBalanceAppliedEvent
     */
    public function withdrawBalanceApplied($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        $withdraw_set = $this->getWithdrawSet();
        if ($withdraw_set['audit_free'] == 1) {

            $free_audit = ['converge_pay'];
            if (in_array($withdrawModel->pay_way, $free_audit)) {

                $job = new WithdrawBalanceAuditFreeJob($withdrawModel);
                dispatch($job);
            }
        }
    }

    private function getWithdrawSet()
    {
        return Setting::get('withdraw.balance');
    }

}