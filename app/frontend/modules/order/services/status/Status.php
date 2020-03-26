<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午11:34
 */

namespace app\frontend\modules\order\services\status;

use app\common\models\Order;

abstract class Status
{
    const PAY = 1; // 支付
    const COMPLETE = 5; // 确认收货
    const EXPRESS = 8; // 查看物流
    const CANCEL = 9; // 取消订单
    const COMMENT = 10; // 评论
    const DELETE = 12; // 删除订单
    const REFUND = 13; // 申请退款
    const REFUND_INFO = 18; // 已退款/退款中
    const COMMENTED = 19; // 已评价
    const STORE_PAY = 20; // 确认核销(核销员)
    const REMITTANCE_RECORD = 21; // 转账信息
    const STORE_MANAGER_PAY = 22;// 店长确认支付
    const STORE_MANAGER_SEND = 23;// 店长确认发货
    const STORE_MANAGER_CANCEL_SEND = 24;// 店长取消发货
    const STORE_MANAGER_COMPLETE = 25;// 店长确认收货
    const STORE_MANAGER_CLOSE = 26;// 店长关闭订单

    abstract function getStatusName();


    /**
     * 退款按钮
     * @param $order
     * @return array
     */
    public static function getRefundButtons(Order $order)
    {
        if ($order['status'] >= Order::COMPLETE) {
            // 完成后不许退款
            if (\Setting::get('shop.trade.refund_days') === '0') {
                return [];
            }
            // 完成后n天不许退款
            if ($order->finish_time->diffInDays() > \Setting::get('shop.trade.refund_days')) {
                return [];
            }
        }
        if($order['status'] <= Order::WAIT_PAY){
            return [];
        }
        if (!empty($order->refund_id) && isset($order->hasOneRefundApply)) {
            // 退款处理中
            if ($order->hasOneRefundApply->isRefunded()) {
                $result[] = [
                    'name' => '已退款',
                    'api' => 'refund.detail',
                    'value' => static::REFUND_INFO
                ];
            } else {
                $result[] = [
                    'name' => '退款中',
                    'api' => 'refund.detail',
                    'value' => static::REFUND_INFO
                ];
            }

        } else {
            // 可申请
            $result[] = [
                'name' => '申请退款',
                'api' => 'refund.apply',
                'value' => static::REFUND
            ];

        }

        return $result;
    }

    /**
     * 评论按钮
     * @param $orderGoods
     * @return array
     */
    public static function getCommentButtons($orderGoods)
    {

        if ($orderGoods->comment_status == 0) {
            $result[] = [
                'name' => '评价',
                'api' => '',
                'value' => static::COMMENT
            ];
        } else {
            $result[] = [
                'name' => '已评价',
                'api' => '',
                'value' => static::COMMENTED
            ];
        }

        return $result;
    }
}
