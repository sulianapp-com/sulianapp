<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\common\models\order;

use app\common\models\BaseModel;
use Carbon\Carbon;

/**
 * Class OrderCoupon
 * @package app\common\models\order
 * @property int uid
 * @property int order_id
 * @property int coupon_id
 * @property int member_coupon_id
 * @property string name
 * @property float amount
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class OrderCoupon extends BaseModel
{
    public $table = 'yz_order_coupon';
    protected $fillable = [];
    protected $guarded = ['id'];
}