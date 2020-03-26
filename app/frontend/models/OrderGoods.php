<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/21
 * Time: 下午1:58
 */

namespace app\frontend\models;

use app\frontend\models\goods\Sale;

/**
 * Class OrderGoods
 * @package app\frontend\models
 * @property GoodsOption goodsOption
 * @property Goods belongsToGood
 */
class OrderGoods extends \app\common\models\OrderGoods
{

    public function scopeDetail($query)
    {
        return $query->select(['id', 'order_id', 'goods_option_title', 'goods_id', 'goods_price', 'total', 'price', 'title', 'thumb', 'comment_status']);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class, 'goods_id', 'goods_id');
    }

    public function getButtonsAttribute()
    {
        $result = [];
        if ($this->comment_status == 1) {
            $result[] = [
                'name' => '查看评价',
                'api' => '',
                'value' => ''
            ];
        }
        return $result;
    }

    public static function getMyCommentList($status)
    {
        $list = self::select()->Where('comment_status', $status)->orderBy('id', 'desc')->get();
        return $list;
    }


}