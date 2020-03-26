<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\common\models\order;

use app\common\models\BaseModel;

/**
 * Class OrderGoodsDiscount
 * @package app\common\models\orderGoods
 * @property int id
 * @property int uid
 * @property int order_id
 * @property int code
 * @property float amount
 * @property int name
 * @property float coin
 */
class OrderCoinExchange extends BaseModel
{
    public $table = 'yz_order_coin_exchange';
    protected $fillable = [];
    protected $guarded = ['id'];
}