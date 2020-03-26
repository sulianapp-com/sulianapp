<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 上午10:45
 */

namespace app\common\models;

use app\backend\models\BackendModel;

class PayRequestDataLog extends BackendModel
{
    public $table = 'yz_pay_request_data';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = ['uniacid', 'out_order_no', 'order_id', 'params', 'type', 'third_type', 'price'];
}