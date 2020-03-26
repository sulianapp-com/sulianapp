<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\exceptions\AppException;
use app\common\modules\process\events\AfterProcessStateChangedEvent;
use app\common\modules\process\events\AfterProcessStatusChangedEvent;
use app\common\traits\HasProcessTrait;
use app\common\traits\CanPendingTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 进程
 * Class ModelHasFlow
 * @package app\common\models\flow
 * @property Flow flow
 * @property Status status
 * @property int id
 * @property int model_id
 * @property int status_id
 * @property int is_pending
 * @property string name
 * @property string code
 * @property \Illuminate\Support\Collection allState
 * @property string state
 * @property string model_type
 * @property string status_name
 * @property string state_name
 * @property string note
 */
class Process extends BaseModel
{
    use HasProcessTrait, SoftDeletes;
    public $table = 'yz_process';

    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];
    protected $appends = ['name', 'status_name'];
    protected $hidden = ['flow', 'model_type'];
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    /**
     * 进程的主体
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function model()
    {
        return $this->belongsTo($this->model_type, 'model_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * 所属流程类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'uid');
    }

    /**
     * 初始化状态
     * @throws \Exception
     */
    public function initStatus()
    {
        // 流程的第一个情况
        $firstStatus = $this->flow->getFirstStatus();

        $this->setStatus($firstStatus);
    }

    /**
     * 进入下一个状态
     * @throws \Exception
     */
    public function toNextStatus()
    {
        // 流程的下一个情况

        $nextStatus = $this->flow->getNextStatus($this->status);

        // 根据情况生成新状态
        $this->setStatus($nextStatus);
    }

    /**
     * @throws AppException
     */
    private function operationValidate()
    {
        // todo 是否可以考虑继续提取一个操作类
        if (isset($this->state) && $this->state != self::STATUS_PROCESSING) {
            throw new AppException("{$this->name}状态为{$this->status->name},无法继续操作");
        }

        if ($this->isPending() == true) {
            throw new AppException("{$this->name}已挂起,无法继续操作");
        }
    }

    /**
     * @param $value
     */
    public function setStateAttribute($value){
        $this->attributes['state'] = $value;
        event(new AfterProcessStateChangedEvent($this));
    }
    /**
     * @param Status $status
     * @throws AppException
     */
    private function setStatus($status)
    {
        $this->operationValidate();

        if ($status->is($this->flow->getFinalStatus())) {
            // 流程执行完
            $this->state = self::STATUS_COMPLETED;
        }
        if ($status->is($this->flow->getCancelStatus()) || $status->is($this->flow->getCloseStatus())) {
            // 流程执行完
            $this->state = self::STATUS_CANCELED;
        }

        $this->setRelation('status',$status);
        $this->status_id = $status->id;

        $this->save();

        event(new AfterProcessStatusChangedEvent($this));
    }

    /**
     * @throws \Exception
     */
    public function toCloseStatus()
    {
        $closeStatus = $this->flow->getCloseStatus();

        $this->setStatus($closeStatus);

    }

    /**
     * @throws \Exception
     */
    public function toCancelStatus()
    {
        $cancelStatus = $this->flow->getCancelStatus();
        $this->setStatus($cancelStatus);

    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->flow->name;
    }

    /**
     * @return string
     */

    public function getStateNameAttribute()
    {
        return $this->allState[$this->state];
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {

        return $this->status->name;
    }

    /**
     * @return int
     */
    public function isPending()
    {
        return $this->is_pending;
    }

    /**
     * @return string
     */
    public function getCodeAttribute()
    {
        return $this->flow->code;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllStateAttribute()
    {
        return collect([
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELED => '已取消',
        ]);
    }

}
