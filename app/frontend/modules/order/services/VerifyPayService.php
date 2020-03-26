<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:48
 */

namespace app\frontend\modules\order\services;


use app\common\models\CorePayLog;
use app\common\models\Order;
use app\frontend\models\OrderLogModel;

class VerifyPayService
{
    public static function verifyPay($order_id)
    {
        if (!$order_id) {
            return '参数错误!';
        }

        $db_order_model = Order::find($order_id);

        if (!$db_order_model) {
            return '订单未找到!';
        }
        if ($db_order_model->status == -1) {
            return '订单已关闭, 无法付款!';
        } else if ($db_order_model->status >= 1) {
            return '订单已付款, 无需重复支付!';
        }
        return '';
    }

    public static function verifyLog(Order $order)
    {
        $db_log_model = OrderLogModel::getLog($order);
        if ($db_log_model && $db_log_model->status != '0') {
            return '订单已支付, 无需重复支付!';
        }
        if ($db_log_model && $db_log_model->status == '0') {
            $db_log_model->delete();
            $db_log_model = null;
        }
        if (!$db_log_model) {
            OrderLogModel::createLog($order);
        }
        return '';
    }
}