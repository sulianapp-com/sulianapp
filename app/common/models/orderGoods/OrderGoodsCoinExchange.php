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
 * @property int code
 * @property float amount
 * @property string coin
 */
class OrderGoodsCoinExchange extends BaseModel
{
    public $table = 'yz_order_goods_coin_exchange';
    protected $fillable = [];
    protected $guarded = ['id'];
}