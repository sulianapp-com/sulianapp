<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/18
 * Time: 9:04
 */

namespace app\common\models\order;

use app\common\models\BaseModel;
 class Invoice extends BaseModel
{
    public $table = 'yz_order_invoice';
     protected $guarded=[];
    public $timestamps = false;
    public static function getData($order_id)
    {
        return self::where('order_id', $order_id)->first();
    }
}