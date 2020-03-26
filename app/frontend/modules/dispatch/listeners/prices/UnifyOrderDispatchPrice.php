<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\goods\GoodsDispatch;
use app\frontend\models\OrderGoods;
use app\frontend\modules\order\services\OrderService;

class UnifyOrderDispatchPrice
{
    private $event;

    public function handle(OrderDispatchWasCalculated $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        // 统一运费取所有商品统一运费的最大值
        $price = $event->getOrderModel()->orderGoods->unique('goods_id')->max(function ($orderGoods) {
            /**
             * @var $orderGoods OrderGoods
             */
            if($orderGoods->isFreeShipping())
            {
                // 免邮费
                return 0;
            }

            if(!isset($orderGoods->goods->hasOneGoodsDispatch)){
                // 没有找到商品配送关联模型
                return 0;
            }
            if ($orderGoods->goods->hasOneGoodsDispatch->dispatch_type == GoodsDispatch::UNIFY_TYPE) {
                // 商品配送类型为 统一运费
                return $orderGoods->goods->hasOneGoodsDispatch->dispatch_price;
            }
            return 0;
        });
        $data = [
            'price' => $price,
            'type' => GoodsDispatch::UNIFY_TYPE,
            'name' => '统一运费',
        ];
        //返回给事件
        $event->addData($data);
        return;
    }

    private function needDispatch()
    {
        // 虚拟物品不需要配送
        if ($this->event->getOrderModel()->is_virtual) {
            return false;
        }

        return true;
    }

}