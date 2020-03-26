<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 上午11:42
 */

namespace app\common\models;

use app\backend\modules\goods\observers\SaleObserver;

class Sale extends BaseModel
{
    public $table = 'yz_goods_sale';

    public $attributes = [
        'max_point_deduct' => '',
        'min_point_deduct' => '',
        'max_balance_deduct' => 0,
        'is_sendfree' => 0,
        'ed_num' => '',
        'ed_full' => 0,
        'ed_reduction' => 0,
        'ed_money' => '',
        'point' => '',
        'bonus' => 0
    ];

    protected $guarded = [''];

    public static function boot()
    {
        parent::boot();
        //注册观察者
        static::observe(new SaleObserver);
    }
}