<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 下午1:49
 */

namespace app\common\models\order;


use app\common\models\BaseModel;

class OrderOperationLog extends BaseModel
{
    public $table = 'yz_order_operation_log';
    protected $guarded = [''];

    public static function insertOrderOperationLog($log)
    {
        OrderOperationLog::create($log);
    }

    public static function getOrderOperationLog($order_id)
    {
        $log = OrderOperationLog::Uniacid()->where('order_id', '=', $order_id)->get();
        return $log;
    }
}