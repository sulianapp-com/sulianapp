<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/16
 * Time: 上午10:53
 */

namespace app\backend\modules\process\controllers;


use app\backend\models\Process;

trait Operate
{
    /**
     * @var Process
     */
    protected $process;

    protected function getProcess(){
        if(!isset($this->process)){
            $processId = request()->input('process_id');

            $this->process = Process::find($processId);
        }
        return $this->process;
    }

    /**
     * @throws \Exception
     */
    public function toNextState()
    {
        $data = $this->getProcess()->toNextStatus();
        return $data;
    }

    /**
     * @throws \Exception
     */
    public function toClosedState(){
        $data = $this->getProcess()->toCloseStatus();
        return $data;
    }
}