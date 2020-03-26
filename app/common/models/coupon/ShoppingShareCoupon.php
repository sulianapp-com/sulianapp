<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 10:49
 */

namespace app\common\models\coupon;

use app\common\models\BaseModel;
use app\framework\Database\Eloquent\Builder;

class ShoppingShareCoupon extends BaseModel
{
    public $table = 'yz_shopping_share_coupon';

    protected $guarded = ['id'];

    protected $attributes = [
        'status' => 0,
    ];


    protected $casts = [
        'share_coupon' => 'json',
        'receive_coupon' => 'json',
    ];


    public function scopeMemberId($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }
}