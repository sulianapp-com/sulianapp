<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:25
 */

namespace app\frontend\modules\dispatch\models;

use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\frontend\modules\dispatch\discount\EnoughReduce;
use app\frontend\modules\dispatch\discount\LevelFreeFreight;
use app\frontend\modules\order\models\PreOrder;


class OrderDispatch
{
    /**
     * @var PreOrder
     */
    private $order;
    /**
     * @var float
     */
    private $freight;

    /**
     * OrderDispatch constructor.
     * @param PreOrder $preOrder
     */
    public function __construct(PreOrder $preOrder)
    {
        $this->order = $preOrder;
    }

    /**
     * 订单运费
     * @return float|int
     */
    public function getFreight()
    {
        if (!isset($this->freight)) {
            if (!isset($this->order->hasOneDispatchType) || !$this->order->hasOneDispatchType->needSend()) {
                // 没选配送方式 或者 不需要配送配送
                return 0;
            }

            // todo 临时解决，是柜子的不算运费
            if (!empty($this->order->mark)) {
                return 0;
            }
            // todo 这里不该使用事件,使用策略比较好
            $event = new OrderDispatchWasCalculated($this->order);
            event($event);
            $data = $event->getData();
            $this->freight = array_sum(array_column($data, 'price'));
            $this->freight = max($this->freight - (new EnoughReduce($this->order))->getAmount(), 0);
            $this->freight = max($this->freight - (new LevelFreeFreight($this->order))->getAmount(), 0);

        }

        return $this->freight;
    }
}