<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/8
 * Time: 下午2:24
 */

namespace app\common\models\order;



use app\common\models\BaseModel;

class OrderSetting extends BaseModel
{
    public $table = 'yz_order_setting';
    protected $hidden = ['id', 'order_id'];
    protected $guarded = [''];

    protected $casts = [
        'value' => 'json'
    ];
    public function get($key){
        dd($key);
        exit;

    }
}