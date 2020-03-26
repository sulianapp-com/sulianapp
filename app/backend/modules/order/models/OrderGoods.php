<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/8
 * Time: 下午5:06
 */

namespace app\backend\modules\order\models;


use app\backend\modules\goods\models\Goods;

class OrderGoods extends \app\common\models\OrderGoods
{
    static protected $needLog = true;
    protected $with = ['goods'];
    protected $appends = [
        'goods_thumb', 'buttons'
    ];

    public function getGoodsThumbAttribute()
    {
        return yz_tomedia($this->goods->thumb);
    }

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }
}