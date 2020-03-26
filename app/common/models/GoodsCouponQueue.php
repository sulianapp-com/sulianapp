<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/13
 * Time: 下午3:10
 */

namespace app\common\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsCouponQueue extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_goods_coupon_queue';

    public static function getCouponQueue()
    {
        return self::uniacid()
            ->with('hasOneCoupon')
            ->with('hasOneMember')
            ->where('status',0);
    }
    
    public static function updatedData($condition, $updatedData)
    {
        return self::where($condition)
            ->update($updatedData);
    }

    public function hasOneCoupon()
    {
        return $this->hasOne('app\common\models\Coupon', 'id', 'coupon_id');
    }
    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'uid');
    }
}