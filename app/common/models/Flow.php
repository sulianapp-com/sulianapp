<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\Collection;

/**
 * 流程类型
 * Class Flow
 * @inheritdoc
 * @package app\common\models\statusFlow
 * @property Collection process
 * @property int id
 * @property string name
 * @property string code
 * @property Collection allStatus
 */
class Flow extends BaseModel
{
    public $table = 'yz_flow';

    protected $guarded = ['id'];

    /**
     * 包含的状态类型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allStatus()
    {
        return $this->hasMany(Status::class,'flow_id')->orderBy('order');
    }

    /**
     * 此状态的下一个状态
     * @param Status $status
     * @return mixed
     */
    public function getNextStatus(Status $status)
    {
        $flowStatus = $this->allStatus->where('id',$status->id)->first();

        return $this->allStatus->where('order', '>', $flowStatus->order)->sortBy('order')->first();
    }

    public function getCloseStatus()
    {
        return $this->allStatus->where('order', Status::ORDER_CLOSE)->first();

    }

    public function getCancelStatus()
    {
        return $this->allStatus->where('order', Status::ORDER_CANCEL)->first();

    }
    /**
     * 获取初始状态
     * @return mixed
     */
    public function getFirstStatus()
    {
        return $this->allStatus->where('order','>=',0)->sortBy('order')->first();
    }
    /**
     * 获取最终状态
     * @return mixed
     */
    public function getFinalStatus()
    {
        return $this->allStatus->where('order','>=',0)->sortByDesc('order')->first();
    }

    /**
     * 添加一组状态
     * @param $statusCollection
     */

    public function pushManyStatus($statusCollection)
    {
        $statusCollection = collect($statusCollection)->map(function ($status) {
            return new Status($status);
        });
        $this->allStatus()->saveMany($statusCollection);
    }

    public function setManyStatus($statusCollection)
    {
        $statusCollection = collect($statusCollection)->map(function ($status) {
            return new Status($status);
        });
        $this->allStatus()->saveMany($statusCollection);

    }

    public function process()
    {
        return $this->hasMany(Process::class, 'flow_id');
    }
}
