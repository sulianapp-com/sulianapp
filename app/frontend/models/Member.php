<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午5:40
 */

namespace app\frontend\models;


use app\common\exceptions\AppException;
use app\common\exceptions\MemberNotLoginException;
use app\common\models\MemberCoupon;
use app\frontend\modules\member\models\MemberAddress;
use app\frontend\modules\member\services\MemberService;
use app\frontend\repositories\MemberAddressRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Member
 * @package app\frontend\models
 * @property Collection memberCarts
 * @property MemberAddress defaultAddress
 */
class Member extends \app\common\models\Member
{

    /**
     * @return self
     * @throws AppException
     */
    public static function current()
    {
        if (!isset(static::$current)) {
            static::$current = self::find(\YunShop::app()->getMemberId());
            if(!static::$current){
                throw new MemberNotLoginException('请登录', $_SERVER['QUERY_STRING']);
            }
        }
        return static::$current;
    }

    /**
     * 会员－会员优惠券1:多关系
     * @param null $backType
     * @return mixed
     */
    public function hasManyMemberCoupon($backType = null)
    {
        return $this->hasMany(MemberCoupon::class, 'uid', 'uid')
            ->where('used', 0)->with(['belongsToCoupon'=> function ($query) use ($backType) {
                if (isset($backType)) {
                    $query->where('coupon_method', $backType);
                }
            }]);
    }

    public function defaultAddress()
    {
        return $this->hasOne(app(MemberAddressRepository::class)->model(), 'uid', 'uid')->where('isdefault', 1);
    }

    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'uid', 'uid');
    }

    public function yzMember()
    {
        return $this->hasOne(self::getNearestModel('MemberShopInfo'), 'member_id', 'uid');
    }

    public function memberCarts()
    {
        return $this->hasMany(app('OrderManager')->make('MemberCart'), 'member_id', 'uid')->with('member');
    }
}