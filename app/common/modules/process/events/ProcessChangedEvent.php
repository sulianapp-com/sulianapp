<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:37
 */

namespace app\common\modules\process\events;

use app\common\events\Event;
use app\common\models\Process;

class ProcessChangedEvent extends Event
{
    /**
     * @var Process
     */
    protected $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function getProcess()
    {
        return $this->process;
    }
}