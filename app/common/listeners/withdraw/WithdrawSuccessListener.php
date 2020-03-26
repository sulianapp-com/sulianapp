<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/5 下午5:36
 * Email: livsyitian@163.com
 */

namespace app\common\listeners\withdraw;


use app\common\events\withdraw\WithdrawSuccessEvent;
use app\common\services\withdraw\PayedService;
use Illuminate\Contracts\Events\Dispatcher;

class WithdrawSuccessListener
{
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen(WithdrawSuccessEvent::class, static::class . "@withdrawSuccess");
    }


    /**
     * @param $event WithdrawSuccessEvent
     * @throws \app\common\exceptions\ShopException
     */
    public function withdrawSuccess($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        (new PayedService($withdrawModel))->confirmPay();
    }
}
