<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/20 0020
 * Time: 下午 3:48
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class GoodsLimitBuy extends BaseModel
{
    protected $table = 'yz_goods_limitbuy';

    public $timestamps = false;

    protected $guarded = [''];

    static function getDataByGoodsId($goods_id)
    {
        return self::uniacid()
            ->where('goods_id', $goods_id)
            ->first();
    }
}
