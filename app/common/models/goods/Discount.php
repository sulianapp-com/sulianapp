<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 下午7:16
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;
use app\backend\modules\goods\observers\DiscountObserver;

class Discount extends BaseModel
{
    public $table = 'yz_goods_discount';
    protected $guarded = ['created_at', 'updated_at'];

    public static function getGoodsDiscountList($goodsId)
    {
        $goodsDiscountInfo = self::where('goods_id', $goodsId)
            ->get();
        return $goodsDiscountInfo;
    }


    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public  function atributeNames()
    {
        return [
//            'level_discount_type' => '等级方式',
//            'discount_method' => '折扣方式',
//            'level_id' => '会员等级id',
//            'discount_value' => '折扣或金额数值'
        ];
    }


    public  function rules()
    {
        return [
//            'level_discount_type' => 'numeric',
//            'discount_method' => 'numeric',
//            'level_id' => 'integer',
//            'discount_value' => 'numeric'
        ];
    }

    public static function boot()
    {
        parent::boot();
        //注册观察者
        static::observe(new DiscountObserver);
    }
}