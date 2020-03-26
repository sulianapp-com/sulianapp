<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午11:20
 */

namespace app\common\modules\status;

use app\common\modules\payType\remittance\models\status\RemittanceAuditStatus;
use app\common\modules\payType\remittance\models\status\RemittanceStatus;
use app\common\modules\process\events\AfterProcessStatusChangedEvent;
use Illuminate\Container\Container;

class StatusContainer extends Container
{

    /**
     * StatusContainer constructor.
     */
    public function __construct()
    {
        $this->setBinds();
    }

    public function handle(AfterProcessStatusChangedEvent $event)
    {

        if ($this->bound($event->getProcess()->code)) {
            $this->make($event->getProcess()->code)->handle($event->getProcess());
        }

    }

    public function setBinds()
    {
//        collect(\app\common\modules\shop\ShopConfig::current()->get('status'))->each(function ($item,$key) {
//            $this->bind($key, function (StatusContainer $container) use ($item) {
//                return new $item();
//
//            });
//        });

        collect(\app\common\modules\shop\ShopConfig::current()->get('shop-foundation.status'))->each(function ($item) {
            $this->bind($item['key'], function (StatusContainer $container) use ($item) {
                return new $item['class']();

            });
        });
    }
}