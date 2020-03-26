<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/8
 * Time: 下午2:24
 */

namespace app\common\models\orderGoods;

use app\common\models\BaseModel;

class OrderGoodsExpansion extends BaseModel
{
    public $table = 'yz_order_goods_expansion';
    protected $hidden = ['id'];
    protected $guarded = ['id'];
}