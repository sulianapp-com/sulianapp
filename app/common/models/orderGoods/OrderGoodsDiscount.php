<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\common\models\orderGoods;

use app\common\models\BaseModel;

/**
 * Class OrderGoodsDiscount
 * @package app\common\models\orderGoods
 * @property int id
 * @property int uid
 * @property int is_indirect
 * @property float amount
 * @property string name
 * @property string discount_code
 * @property int order_goods_id
 */
class OrderGoodsDiscount extends BaseModel
{
    public $table = 'yz_order_goods_discount';
    protected $fillable = [];
    protected $guarded = ['id'];
}