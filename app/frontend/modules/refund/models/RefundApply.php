<?php

namespace app\frontend\modules\refund\models;

use app\frontend\models\Order;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 下午9:53
 */
class RefundApply extends \app\common\models\refund\RefundApply
{
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('uid', \YunShop::app()->getMemberId());
        });
    }

    public function scopeDefaults($query)
    {
        return $query->with([
            'order' => function ($query) {
                return $query->orders();
            }
        ])->orderBy('id', 'desc');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}