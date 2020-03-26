<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\common\models\orderGoods;

use app\common\models\BaseModel;

class OrderGoodsDeduction extends BaseModel
{
    public $table = 'yz_order_goods_deduction';
    protected $fillable = [];
    protected $guarded = ['id'];
}