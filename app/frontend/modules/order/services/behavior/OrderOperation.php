<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 订单操作基类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\exceptions\AppException;
use app\common\models\Order;

abstract class OrderOperation extends Order
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var array 合法前置状态
     */
    protected $statusBeforeChange = [];

    /**
     * @var string 类名的过去式
     */
    protected $past_tense_class_name;

    /**
     * @var string 操作名
     */
    protected $name;

    /**
     * 获取不带命名空间的类名
     * @return mixed
     */
    private function _getOperationName()
    {
        $result = explode('\\', static::class);
        return end($result);
    }

    /**
     * @return string 类名的过去式
     */
    protected function _getPastTense()
    {
        return $this->past_tense_class_name;
    }

    /**
     * @return \app\common\events\order\CreatedOrderEvent
     */
    protected function getBeforeEvent()
    {
        $event_name = '\app\common\events\order\Before' . $this->_getOperationName() . 'Event';
        return new $event_name($this);
    }

    /**
     * 是否满足操作条件
     * @return bool
     * @throws AppException
     */
    private function check()
    {
        $event = $this->getBeforeEvent();
        event($event);

        if ($this->refund_id > 0) {
            if ($this->hasOneRefundApply->isRefunding()) {
                throw new AppException("退款中的订单,无法执行{$this->name}操作");
            }
        }

        if (!in_array($this->status, $this->statusBeforeChange)) {

            throw new AppException("ID:{$this->id}订单状态不满足{$this->name}操作");
        }
        return true;
    }

    /**
     * @throws AppException
     */
    public function handle(){
        $this->check();
    }

    protected function _fireEvent()
    {
        $event_name = '\app\common\events\order\After' . $this->_getPastTense() . 'Event';
        event(new $event_name($this));
    }
}