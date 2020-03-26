<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 14/06/2018
 * Time: 11:44
 */

namespace app\common\traits;

use app\common\exceptions\AppException;
use app\common\models\Flow;
use app\common\models\Process;
use app\frontend\modules\member\services\MemberService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasFlowTrait
 * @package app\common\traits
 * @property Collection process
 * @property Collection flows
 * @property Process flow
 */
trait HasProcessTrait
{
    /**
     * @var Process
     */
    protected $currentProcess;

    /**
     * 所有的流程类型
     * @return mixed
     */
    public function flows()
    {
        return $this->morphToMany(
            Flow::class,
            'model',
            (new Process())->getTable(),
            'model_id',
            'flow_id'
        )->withTimestamps();
    }

    /**
     * @param Flow $flow
     * @return array
     */
    private function processAttribute(Flow $flow)
    {
        return ['uid' => \YunShop::app()->getMemberId(),
            'flow_id' => $flow->id,
            'model_id' => $this->id,
            'model_type' => $this->getMorphClass(),
            'uniacid' => \YunShop::app()->uniacid
        ];
    }

    /**
     * @param Flow $flow
     * @throws \Exception
     */
    public function addProcess(Flow $flow)
    {
        if ($this->currentProcess()->code == $flow->code) {
            throw new AppException("已存在未完成的{$this->currentProcess()->name}流程,无法继续添加");
        }
        $this->currentProcess = $this->createProcess($flow);

        $this->currentProcess;
    }

    /**
     * @param Flow $flow
     * @return Process
     * @throws \Exception
     */
    protected function createProcess(Flow $flow)
    {
        $process = new Process($this->processAttribute($flow));
        $process->initStatus();
        return $process;

    }

    /**
     * @return HasMany
     */
    public function process()
    {
        return $this->hasMany(Process::class, 'model_id')->where('model_type', $this->getMorphClass());
    }

    /**
     * 当前的流程
     * @return Process
     */
    public function currentProcess()
    {
        if (!isset($this->currentProcess)) {
            if ($this->process->isEmpty()) {
                return null;
            }

            $this->currentProcess = $this->process->where('state', 'processing')->first();
        }
        return $this->currentProcess;
    }
}