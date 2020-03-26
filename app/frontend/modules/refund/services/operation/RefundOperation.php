<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 订单操作基类
 */

namespace app\frontend\modules\refund\services\operation;

use app\common\exceptions\AppException;
use app\common\models\refund\RefundApply;


abstract class RefundOperation extends RefundApply
{
    /**
     * @var array 合法前置状态
     */
    protected $statusBeforeChange = [];

    /**
     * @var string 类名的过去式
     */
    protected $pastTenseClassName;
    /**
     * @var string 操作名
     */
    protected $name;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('无效申请,该订单属于其他用户');
        }

    }

    /**
     * 获取不带命名空间的类名
     * @return mixed
     */
    private function getOperationName()
    {
        $result = explode('\\', static::class);
        return end($result);
    }

    /**
     * @return string 类名的过去式
     */
    protected function getPastTense()
    {
        return $this->pastTenseClassName;
    }

    /**
     * @return \app\common\events\order\CreatedOrderEvent
     */
    protected function getBeforeEvent()
    {
        $eventName = '\app\common\events\order\Before' . $this->getOperationName() . 'Event';
        return new $eventName($this->order);
    }

    /**
     * 是否满足操作条件
     * @return bool
     * @throws AppException
     */
    public function enable()
    {
        if (!in_array($this->status, $this->statusBeforeChange)) {
            throw new AppException($this->status_name."的售后申请,无法执行{$this->name}操作");
        }
        return true;
    }

    /**
     * 执行订单操作
     * @return mixed
     */
    abstract public function execute();

    /**
     *
     */
    protected function fireEvent()
    {
        $eventName = '\app\common\events\order\After' . $this->getPastTense() . 'Event';
        event(new $eventName($this->order));
        return;
    }
}