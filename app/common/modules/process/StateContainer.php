<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/29
 * Time: 下午3:31
 */

namespace app\common\modules\process;

use app\common\modules\payType\remittance\models\state\RemittanceState;
use app\common\modules\process\events\AfterProcessStateChangedEvent;
use Illuminate\Container\Container;

class StateContainer extends Container
{

    /**
     * StatusContainer constructor.
     */
    public function __construct()
    {
        $this->setBinds();
    }

    public function handle(AfterProcessStateChangedEvent $event)
    {
        if ($this->bound($event->getProcess()->code)) {
            $this->make($event->getProcess()->code)->handle($event->getProcess());
        }

    }

    public function setBinds()
    {
        collect([
            [
                'key' => 'remittance',
                'class' => RemittanceState::class,
            ]
        ])->each(function ($item) {
            $this->bind($item['key'], function ($container) use ($item) {
                return new $item['class']();

            });
        });

    }
}