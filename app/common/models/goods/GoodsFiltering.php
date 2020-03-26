<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/30
 */

namespace app\common\models\goods;

use app\common\models\BaseModel;

class GoodsFiltering extends BaseModel
{

    public $table = 'yz_goods_filtering';

    public $timestamps = false;

    protected $guarded = [];

    public function scopeOfGoodsId($query, $goodsId)
    {
        return $query->where('goods_id', $goodsId);
    }
}