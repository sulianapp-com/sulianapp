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
 * Class OrderFee
 * @package app\common\models\order
 * @property string name
 * @property string fee_code
 * @property float amount
 * @property int uid
 * @property int order_id
 */
class OrderFee extends BaseModel
{
    public $table = 'yz_order_fee';
    protected $fillable = [];
    protected $guarded = ['id'];
}