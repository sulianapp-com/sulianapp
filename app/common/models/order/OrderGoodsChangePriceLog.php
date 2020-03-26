<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/18
 * Time: 下午9:14
 */

namespace app\common\models\order;


use app\common\models\BaseModel;
use app\common\models\OrderGoods;

class OrderGoodsChangePriceLog extends BaseModel
{
    public $table = 'yz_order_goods_change_log';
    protected $fillable = [];
    protected $guarded = ['id'];
    public function belongsToOrderGoods(){
        return $this->belongsTo(OrderGoods::class,'order_goods_id','id');
    }

}