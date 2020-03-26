<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/20 下午4:24
 * Email: livsyitian@163.com
 */

namespace app\common\listeners\withdraw;



use app\common\events\withdraw\WithdrawAuditedEvent;
use app\common\models\Income;
use Illuminate\Contracts\Events\Dispatcher;

class WithdrawAuditListener
{
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen(WithdrawAuditedEvent::class, static::class . "@withdrawAudited", 999);
    }


    /**
     * @param $event WithdrawAuditedEvent
     */
    public function withdrawAudited($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        $audit_ids = $withdrawModel->audit_ids;
        $rebut_ids = $withdrawModel->rebut_ids;
        $invalid_ids = $withdrawModel->invalid_ids;

        if (count($audit_ids) > 0) {
            Income::whereIn('id', $audit_ids)->update(['pay_status' => Income::PAY_STATUS_WAIT]);
        }
        if (count($rebut_ids) > 0) {
            Income::whereIn('id', $rebut_ids)->update(['status' => Income::STATUS_INITIAL, 'pay_status' => Income::PAY_STATUS_REJECT]);
        }
        if (count($invalid_ids) > 0) {
            Income::whereIn('id', $invalid_ids)->update(['pay_status' => Income::PAY_STATUS_INVALID]);
        }
    }
}
