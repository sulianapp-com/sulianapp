<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/16
 * Time: 上午10:53
 */

namespace app\frontend\modules\process\controllers;


use app\common\exceptions\AppException;
use app\frontend\models\Process;

trait Operate
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * @return Process
     */
    abstract protected function _getProcess();
    abstract protected function beforeStates();

    /**
     * @return Process
     * @throws AppException
     */
    protected function getProcess(){
        $this->validate([
            'process_id' => 'integer'
        ]);
        if (!isset($this->process)) {
            $this->process = $this->_getProcess();
            if ($this->process->status->code != $this->beforeStates()) {
                throw new AppException("{$this->process->name}流程处于{$this->process->status->name}状态,无法执行{$this->name}操作");
            }
        }

        return $this->process;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function toNextState()
    {
        $data = $this->getProcess()->toNextStatus();
        return $data;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function toCancelState()
    {
        $data = $this->getProcess()->toCancelStatus();
        return $data;
    }
}