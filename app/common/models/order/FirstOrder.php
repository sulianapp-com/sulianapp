<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-06-09
 * Time: 17:38
 */

namespace app\common\models\order;


use app\common\models\BaseModel;

class FirstOrder extends BaseModel
{
    public $table = 'yz_first_order';
    public $timestamps = true;
    protected $guarded = [''];
    protected $casts = [
        'shop_order_set' => 'json'
    ];
}