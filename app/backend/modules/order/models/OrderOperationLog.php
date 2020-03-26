<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 下午5:37
 */
namespace app\backend\modules\order\models;


class OrderOperationLog extends \app\common\models\order\OrderOperationLog
{
    public static function insertOperationLog( $order_model)
    {
        $log = [
            'order_id'                  => $order_model->id,
            'type'                      => implode('-', [$order_model->getOriginal('status'), $order_model->status]),
            'before_operation_status'   => $order_model->getOriginal('status'),
            'after_operation_status'    => $order_model->status,
            'operator'                  => \YunShop::app()->getMemberId()?\YunShop::app()->getMemberId():'admin',
            'operation_time'            => time()
        ];
        OrderOperationLog::insertOrderOperationLog($log);
    }
}