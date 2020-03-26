<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/11
 * Time: 上午17:10
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\backend\modules\goods\models\Dispatch;
use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\goods\GoodsDispatch;
use app\common\modules\orderGoods\OrderGoodsCollection;
use app\frontend\models\OrderGoods;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;


class TemplateOrderDispatchPrice
{
    private $event;

    /**
     * @var PreOrder
     */
    private $order;

    public function handle(OrderDispatchWasCalculated $event)
    {

        $this->event = $event;

        $this->order = $event->getOrderModel();

        $price = $this->getPrice();

        //dd($price);

        $data = [
            'price' => $price,
            'type' => GoodsDispatch::TEMPLATE_TYPE,
            'name' => '运费模板',
        ];

        //返回给事件
        $event->addData($data);
        return;
    }


    private function getPrice()
    {
        //去掉重复的OrderGoods
        $uniqueOrderGoods = $this->order->orderGoods->unique('goods_id');
        $dispatch_prices = [];
        $dispatch_ids = $this->getDispatchIds($uniqueOrderGoods);
        trace_log()->freight('订单模板运费模板id',json_encode($dispatch_ids));
        foreach ($dispatch_ids as $dispatch_id) {
            $dispatch_prices[] = $this->getDispatchPrice($dispatch_id, $uniqueOrderGoods);
        }


        return max($dispatch_prices);
    }


    /**
     * 通过订单商品集合 获取所用到的配送模版 ID 集
     *
     * @param $orderGoodsCollection
     * @return array
     */
    private function getDispatchIds(OrderGoodsCollection $orderGoodsCollection)
    {
        $dispatch_ids = [];
        foreach ($orderGoodsCollection as $aOrderGoods) {
            /**
             * @var OrderGoods $aOrderGoods
             */
            $goodsDispatch = $aOrderGoods->goods->hasOneGoodsDispatch;

            if ($goodsDispatch->dispatch_type == GoodsDispatch::TEMPLATE_TYPE) {

                $dispatch_id = $goodsDispatch->dispatch_id;
                if (empty($dispatch_id)) {
                    $goodsDispatch->dispatch_id = $this->getDefaultDispatchId();
                }

                if (!in_array($dispatch_id, $dispatch_ids)) {
                    $dispatch_ids[] = $goodsDispatch->dispatch_id;
                }
            }
        }

        return $dispatch_ids;
    }


    private function getDefaultDispatchId()
    {
        $defaultDispatch = Dispatch::getOneByDefault();

        //todo 如果没有默认配送模版 如何处理
        trace_log()->freight('订单模板运费','不存在默认的配送模板');

        return $defaultDispatch->id ?: 0;
    }


    private function getDispatchPrice($dispatch_id, $orderGoods)
    {

        $dispatch_good_total = 0;
        $dispatch_good_weight = 0;


        foreach ($orderGoods as $aOrderGoods) {
            /**
             * @var OrderGoods $aOrderGoods
             */
            //商品满额、满件减免运费
            if ($aOrderGoods->isFreeShipping()) {
                trace_log()->freight('订单模板运费','商品'.$aOrderGoods->goods_id.'免运费');

                continue;
            }

            $dispatchModel = $aOrderGoods->goods->hasOneGoodsDispatch;

            //配送模版不存在
            if (!isset($dispatchModel)) {

                trace_log()->freight('订单模板运费','商品'.$aOrderGoods->goods_id.'运费模板不存在');
                continue;
            }

            //如果是默认配送模版
            if (!$dispatchModel->dispatch_id) {
                $dispatchModel->dispatch_id = $this->getDefaultDispatchId();
            }

            if ($dispatchModel->dispatch_type != GoodsDispatch::TEMPLATE_TYPE) {
                trace_log()->freight('订单模板运费','商品'.$aOrderGoods->goods_id.'配送费计算方式不是运费模板');

                continue;
            }

            if ($dispatchModel->dispatch_id != $dispatch_id) {

                trace_log()->freight('订单模板运费','商品'.$aOrderGoods->goods_id.'配送模板('.$dispatchModel->dispatch_id.')不匹配'.$dispatch_id);

                continue;
            }
            $dispatch_good_total += $this->getGoodsTotalInOrder($aOrderGoods);
            $dispatch_good_weight += $this->getGoodsTotalWeightInOrder($aOrderGoods);
        }

        $amount =  $this->calculation($dispatch_id, $dispatch_good_total, $dispatch_good_weight);

        trace_log()->freight('订单模板运费','配送费'.$amount.'元');
        return $amount;
    }

    /**
     * 累加订单中每个 同类型不同规格商品 的总数
     * @param PreOrderGoods $orderGoods
     * @return mixed
     */
    private function getGoodsTotalInOrder(PreOrderGoods $orderGoods)
    {
        $result = $orderGoods->order->orderGoods->where('goods_id', $orderGoods->goods_id)->sum(function ($orderGoods) {
            return $orderGoods->total;
        });

        return $result;
    }

    /**
     * 累加订单中每个 同类型不同规格商品 的总重量
     * @param PreOrderGoods $orderGoods
     * @return mixed
     */
    private function getGoodsTotalWeightInOrder(PreOrderGoods $orderGoods)
    {
        $result = $orderGoods->order->orderGoods->where('goods_id', $orderGoods->goods_id)->sum(function ($orderGoods) {
            return $orderGoods->total * $orderGoods->getWeight();
        });

        return $result;
    }

    private function calculation($dispatch_id, $dispatch_good_total, $dispatch_good_weight)
    {
        $price = 0;
        if (!$dispatch_id) {
            trace_log()->freight('订单模板运费','配送模板id'.$dispatch_id.'不存在');
            return $price;
        }

        $dispatchModel = Dispatch::getOne($dispatch_id);

        if (!$dispatch_id) {
            trace_log()->freight('订单模板运费','配送模板id'.$dispatch_id.'不存在');
            return $price;
        }

        switch ($dispatchModel->calculate_type) {
            case 1:
                $price = $this->calculationByPiece($dispatchModel, $dispatch_good_total);
                break;
            case 0:
                $price = $this->calculationByWeight($dispatchModel, $dispatch_good_weight);
                break;
        }

        $price = $this->verify($price);

        return $price;
    }

    private function verify($price)
    {
        if (empty($price)) {
            return 0;
        }
        return $price;
    }


    private function calculationByPiece($dispatchModel, $goods_total)
    {
        if (!$goods_total) {
            return 0;
        }
        $piece_data = unserialize($dispatchModel->piece_data);

        // 存在
        if ($piece_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city_id = isset($this->order->orderAddress->city_id) ? $this->order->orderAddress->city_id : 0;

            if (!$city_id) {
                return 0;
            }
            foreach ($piece_data as $key => $piece) {
                $area_ids = explode(';', $piece['area_ids']);
                if (in_array($this->order->orderAddress->city_id, $area_ids)) {
                    $dispatch = $piece;
                    break;
                }
            }

            if ($dispatch) {
                // 找到匹配的数量数据
                if ($goods_total > $dispatch['first_piece']) {
                    $diff = $goods_total - $dispatch['first_piece'];
                    $another_piece = $dispatch['another_piece_price'];
                    if ($diff > 0) {
                        $another_piece = ceil($diff / $dispatch['another_piece']) * $dispatch['another_piece_price'];
                    }
                    return $dispatch['first_piece_price'] + $another_piece;
                } else {
                    return $dispatch['first_piece_price'];
                }
            }
        }
        // 默认件数
        if ($goods_total > $dispatchModel->first_piece) {

            $diff = $goods_total - $dispatchModel->first_piece;
            $another_piece = $dispatchModel->another_piece_price;

            if ($diff > 0) {
                $another_piece = ceil($diff / $dispatchModel->another_piece) * $dispatchModel->another_piece_price;
            }
            return $dispatchModel->first_piece_price + $another_piece;
        } else {
            return $dispatchModel->first_piece_price;
        }
    }

    private function calculationByWeight($dispatchModel, $weight_total)
    {
        if (!$weight_total) {
            return 0;
        }
        $weight_data = unserialize($dispatchModel->weight_data);

        // 存在重量数据
        if ($weight_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city_id = isset($this->order->orderAddress->city_id) ? $this->order->orderAddress->city_id : '';
            if (!$city_id) {
                return 0;
            }

            foreach ($weight_data as $key => $weight) {
                //dd($weight['area_ids']);
                $area_ids = explode(';', $weight['area_ids']);
                if (in_array($city_id, $area_ids)) {
                    $dispatch = $weight;
                    break;
                }
            }

            if ($dispatch) {
                // 找到匹配的重量数据
                if ($weight_total > $dispatch['first_weight']) {
                    // 续重:   首重价格+(重量-首重)/续重*续重价格
                    // 20 + (500 - 400)
                    return $dispatch['first_weight_price'] + ceil(($weight_total - $dispatch['first_weight']) / $dispatch['another_weight']) * $dispatch['another_weight_price'];
                } else {
                    return $dispatch['first_weight_price'];
                }
            }
        }

        // 默认全国重量运费
        if ($weight_total > $dispatchModel->first_weight) {
            return $dispatchModel->first_weight_price + ceil(($weight_total - $dispatchModel->first_weight) / $dispatchModel->another_weight) * $dispatchModel->another_weight_price;
        } else {
            return $dispatchModel->first_weight_price;
        }
    }

}