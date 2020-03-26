<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/21 上午9:49
 * Email: livsyitian@163.com
 */

namespace app\common\listeners\withdraw;


use app\common\events\withdraw\WithdrawPayedEvent;
use app\common\models\Income;
use Illuminate\Contracts\Events\Dispatcher;

class WithdrawPayListener
{
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen(WithdrawPayedEvent::class, static::class . "@withdrawPayed", 999);
    }


    /**
     * @param $event WithdrawPayedEvent
     */
    public function withdrawPayed($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        $income_ids = explode(',', $withdrawModel->type_id);

        if (count($income_ids) > 0) {
            Income::whereIn('id', $income_ids)->where('pay_status', Income::PAY_STATUS_WAIT)->update(['pay_status' => Income::PAY_STATUS_FINISH]);
        }
    }

}
