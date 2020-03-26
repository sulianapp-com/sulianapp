<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 下午3:10
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class GoodsCoupon extends BaseModel
{
    public $table = 'yz_goods_coupon';


    public $timestamps = false;

    protected $guarded = [''];

    protected $casts = [
        'coupon' => 'json',
        'share_coupon' => 'json',
    ];


    public $attributes = [
        'goods_id'  => 0,
        'is_give'   => 0,
        'send_type' => 0,
        'send_num'  => 0,
    ];


    public function scopeOfGoodsId($query,$goodsId)
    {
        return $query->where('goods_id',$goodsId);
    }


    //todo 废弃使用==删除，使用 ofGoodsId() 方法
    /*public static function getGoodsCouponByGoodsId($goodsId)
    {
        return self::where('goods_id',$goodsId);
    }*/


    public function rules()
    {
        return [
            //'goods_id'  => '',
            //'is_give'   => '',
            //'coupon'    => '',
            //'send_type' => '',
            'send_num'  => 'numeric|integer',
        ];
    }

    public function atributeNames()
    {
        return [
            //'goods_id'  => '',
            //'is_give'   => '',
            //'coupon'    => '',
            //'send_type' => '',
            'send_num'  => '优惠券发送次数',
        ];
    }

}