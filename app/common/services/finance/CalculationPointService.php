<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 下午6:27
 */

namespace app\common\services\finance;

use app\common\models\Order;
use Setting;

class CalculationPointService
{
    public static function calcuationPointByGoods($order_goods_model)
    {
        $point_set = Setting::get('point.set');


        $order = Order::find($order_goods_model->order_id);
        $order_set = $order->orderSettings->where('key', 'point')->first();
        if ($order_set && $order_set->value['set']['give_point']) {
            $point_set['give_point'] = $order_set->value['set']['give_point'] . '%';
        }


        $point_data = [];
        //todo 如果等于0  不赠送积分
        if (isset($order_goods_model->hasOneGoods->hasOneSale) && $order_goods_model->hasOneGoods->hasOneSale->point !== '' && intval($order_goods_model->hasOneGoods->hasOneSale->point) === 0) {
            return $point_data;
        }



        //todo 如果不等于空，按商品设置赠送积分，否则按统一设置赠送积分
        if (isset($order_goods_model->hasOneGoods->hasOneSale) && !empty($order_goods_model->hasOneGoods->hasOneSale->point)) {
            if (strexists($order_goods_model->hasOneGoods->hasOneSale->point, '%')) {
                $point_data['point'] = floatval(str_replace('%', '', $order_goods_model->hasOneGoods->hasOneSale->point) / 100 * $order_goods_model->payment_amount);
            } else {
                $point_data['point'] = $order_goods_model->hasOneGoods->hasOneSale->point * $order_goods_model->total;
            }
            $point_data['remark'] = '购买商品[' . $order_goods_model->hasOneGoods->title .'(比例:'. $order_goods_model->hasOneGoods->hasOneSale->point .')]赠送['.$point_data['point'].']积分！';
        } else if (!empty($point_set['give_point'] && $point_set['give_point'])) {
            if (strexists($point_set['give_point'], '%')) {
                $point_data['point'] = floatval(str_replace('%', '', $point_set['give_point']) / 100 * $order_goods_model->payment_amount);
            } else {
                $point_data['point'] = $point_set['give_point'] * $order_goods_model->total;
            }
            $point_data['remark'] = "购买商品[统一设置(比例:". $point_set['give_point'] .")]赠送[{$point_data['point']}]积分！";
        }
        return $point_data;
    }

    public static function calcuationPointByOrder($order_model)
    {
        $point_set = Setting::get('point.set');
        $point_data = [];
        if (isset($point_set['enoughs'])) {
            foreach (collect($point_set['enoughs'])->sortBy('enough') as $enough) {
                $orderPrice = $order_model->price - $order_model->dispatch_price - $order_model->fee_amount;
                if ($orderPrice >= $enough['enough'] && $enough['give'] > 0) {
                    $point_price = $enough['enough'];
                    $point_data['point'] = $enough['give'];
                    $point_data['remark'] = '订单[' . $order_model->order_sn . ']消费满[' . $enough['enough'] . ']元赠送[' . $enough['give'] . ']积分';
                    if ($point_set['point_award_type'] == 1) {
                        $point_data['point'] = $orderPrice * $enough['give'] / 100;
                        $point_data['remark'] = '订单[' . $order_model->order_sn . ']消费满[' . $enough['enough'] . ']元赠送[' . $enough['give'] . '%]积分';
                    }
                }
            }
        }
        if (!empty($point_set['enough_money']) && !empty($point_set['enough_point'])) {
            $orderPrice = $order_model->price - $order_model->dispatch_price - $order_model->fee_amount;
            if ($orderPrice >= $point_set['enough_money'] && $point_set['enough_point'] > 0 && $point_set['enough_money'] > $point_price) {
                $point_data['point'] = $point_set['enough_point'];
                $point_data['remark'] = '订单[' . $order_model->order_sn . ']消费满[' . $point_set['enough_money'] . ']元赠送[' . $point_data['point'] . ']积分';

                if ($point_set['point_award_type'] == 1) {
                    $point_data['point'] = $orderPrice * $point_set['enough_point'] / 100;
                    $point_data['remark'] = '订单[' . $order_model->order_sn . ']消费满[' . $point_set['enough_money'] . ']元赠送[' . $point_data['point'] . '%]积分';
                }
            }
        }
        return $point_data;
    }
}