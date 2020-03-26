<?php

namespace app\common\models\goods;

use app\common\models\BaseModel;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/5/22
*/
class GoodsService extends BaseModel
{

	public $table = 'yz_goods_service';

	public $attributes = [
		'is_refund' => 1,
		'is_automatic' => 0,
        'serviceFee' => 0,
	];

    public $timestamps = false;

    protected $guarded = [];

    public function scopeOfGoodsId($query, $goodsId)
    {
        return $query->where('goods_id', $goodsId);
    }

    /**
     * 初始化方法
     */
    public static function boot()
    {
        parent::boot();
        // 添加了公众号id的全局条件.
        static::addGlobalScope(function ($builder) {
            $builder->uniacid();
        });
    }
}