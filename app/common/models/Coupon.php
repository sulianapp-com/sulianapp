<?php

namespace app\common\models;

use app\framework\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Coupon
 * @package app\common\models
 * @property int coupon_method
 * @property int use_type
 * @property int status
 * @property int get_type
 * @property int time_limit
 * @property string name
 * @property string suppliernames
 * @property array supplierids
 * @property int getsupplier
 * @property string storenames
 * @property array storeids
 * @property int getstore
 * @property string category_ids
 * @property int is_complex
 * @property array goods_ids
 * @property int id
 * @property int plugin_id
 * @property int total
 * @property int time_days
 * @property float discount
 * @property float enough
 * @property float deduct
 * @property int get_max
 * @property int level_limit
 * @property Carbon time_start
 * @property Carbon time_end
 * @method  Builder memberLevel($memberLevel)
 * @method Builder unexpired($time)
 */
class  Coupon extends BaseModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at', 'time_start', 'time_end'];

    const COUPON_SHOP_USE = 0; //适用范围 - 商城通用
    const COUPON_CATEGORY_USE = 1; //适用范围 - 指定分类
    const COUPON_GOODS_USE = 2; //适用范围 - 指定商品
    const COUPON_SUPPLIER_USE = 3; //适用范围 - 指定供应商single
    const COUPON_STORE_USE = 4; //适用范围 - 指定门店
    const COUPON_SINGLE_STORE_USE = 5; //适用范围 - 指定单个门店
    const COUPON_ONE_HOTEL_USE = 6; //适用范围 - 单个酒店
    const COUPON_MORE_HOTEL_USE = 7; //适用范围 - 多个酒店


    const COUPON_MONEY_OFF = 1; //优惠方式- 立减
    const COUPON_DISCOUNT = 2; //优惠方式- 折扣

    const COUPON_DATE_TIME_RANGE = 1;//有效期 - 时间范围
    const COUPON_SINCE_RECEIVE = 0;//有效期 - 领取后n天

    const NO_LIMIT = -1; //不限


    public $table = 'yz_coupon';

    protected $guarded = [];

    protected $casts = [
        'goods_ids' => 'json',
        'category_ids' => 'json',
        'goods_names' => 'json',
        'categorynames' => 'json',
        'supplierids' => 'json',
        'storeids' => 'json',
        'storenames' => 'json',
    ];
//    protected $hidden = ['uniacid', 'cat_id', 'get_type', 'level_limit', 'use_type', 'return_type', 'coupon_type'
//        , 'coupon_method','back_type','supplier_uid','cashiersids','cashiersnames','category_ids','goods_ids',
//    'storeids','supplierids','is_complex','getcashier','getstore','getsupplier','back_money','back_credit',
//        'back_redpack','back_when','descnoset','deleted_at'];

    public function hasManyMemberCoupon()
    {
        return $this->hasMany('app\common\models\MemberCoupon');
    }

    public static function getValidCoupon($MemberModel)
    {
        return MemberCoupon::getMemberCoupon($MemberModel);
    }

    public static function getUsageCount($couponId)
    {
        return static::uniacid()
            ->select(['id'])
            ->where('id', '=', $couponId)
            ->withCount(['hasManyMemberCoupon' => function ($query) {
                return $query->where('used', '=', 0);
            }]);
    }

    public static function getCouponById($couponId)
    {
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->first();
    }

    //获取优惠券优惠方式
    public static function getPromotionMethod($couponId)
    {
        $useType = static::uniacid()->where('id', '=', $couponId)->value('coupon_method');
        switch ($useType) {
            case self::COUPON_MONEY_OFF:
                return [
                    'type' => self::COUPON_MONEY_OFF,
                    'mode' => static::uniacid()->where('id', '=', $couponId)->value('deduct'),
                ];
                break;
            case self::COUPON_DISCOUNT:
                return [
                    'type' => self::COUPON_DISCOUNT,
                    'mode' => static::uniacid()->where('id', '=', $couponId)->value('discount'),
                ];
                break;
            default:
                return [
                    'type' => self::COUPON_SHOP_USE,
                ];
                break;
        }
    }

    //获取优惠券适用期限
    public static function getTimeLimit($couponId)
    {
        $time_limit = static::uniacid()->where('id', '=', $couponId)->value('time_limit');
        switch ($time_limit) {
            case self::COUPON_SINCE_RECEIVE:
                return [
                    'type' => self::COUPON_SINCE_RECEIVE,
                    'time_end' => static::uniacid()->where('id', '=', $couponId)->value('time_days'),
                ];
                break;
            case self::COUPON_DATE_TIME_RANGE:
                return [
                    'type' => self::COUPON_DATE_TIME_RANGE,
                    'time_end' => static::uniacid()->where('id', '=', $couponId)->value('time_end'),
                ];
                break;
            default:
                return [
                    'type' => self::COUPON_SHOP_USE,
                ];
                break;
        }
    }

    //获取优惠券的适用范围
    public static function getApplicableScope($couponId)
    {
        $useType = static::uniacid()
            ->where('id', '=', $couponId)
            ->value('use_type');
        switch ($useType) {
            case 8:
                $goodIds = self::getApplicalbeGoodIds($couponId);
                return [
                    'type' => self::COUPON_GOODS_USE,
                    'scope' => $goodIds,
                ];
                break;
            case self::COUPON_GOODS_USE:
                $goodIds = self::getApplicalbeGoodIds($couponId);
                return [
                    'type' => self::COUPON_GOODS_USE,
                    'scope' => $goodIds,
                ];
                break;
            case self::COUPON_CATEGORY_USE:
                $categoryIds = self::getApplicalbeCategoryIds($couponId);
                return [
                    'type' => self::COUPON_CATEGORY_USE,
                    'scope' => $categoryIds,
                ];
                break;
            case self::COUPON_STORE_USE:
                $categoryIds = self::getApplicalbeCategoryIds($couponId);
                return [
                    'type' => self::COUPON_STORE_USE,
                    'scope' => $categoryIds,
                ];
                break;
            case self::COUPON_SINGLE_STORE_USE:
                $categoryIds = self::getApplicalbeCategoryIds($couponId);
                return [
                    'type' => self::COUPON_SINGLE_STORE_USE,
                    'scope' => $categoryIds,
                ];
                break;
            default:
                return [
                    'type' => self::COUPON_SHOP_USE,
                ];
                break;
        }
    }

    //获取优惠券的适用商品ID
    public static function getApplicalbeGoodIds($couponId)
    {
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->value('goods_ids');
    }

    //获取优惠券的适用商品分类ID
    public static function getApplicalbeCategoryIds($couponId)
    {
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->value('category_ids');
    }

    /**
     * 筛选某会员等级可领
     * @param Builder $query
     * @param $memberLevel
     * @return Builder
     */
    public function scopeMemberLevel(Builder $query, $memberLevel)
    {
        return $query->where(function ($query) use ($memberLevel) {
            $query->where('level_limit', '<=', $memberLevel)
                ->orWhere(function ($query) {
                    $query->where('level_limit', -1);
                });
        });
    }

    /**
     * 筛选未过期
     * @param Builder $query
     * @param null $time
     * @return Builder
     */
    public function scopeUnexpired(Builder $query, $time = null)
    {
        if (!isset($time)) {
            $time = time();
        }
        return $query->where(function ($query) use ($time) {
            // 不限时间
            $query->where('time_limit', 1)->where('time_end', '>', $time)
                ->orWhere(function ($query) {
                // 未结束的优惠券
                $query->where('time_limit', 0);
            });

        });
    }

    /**
     * todo 为什么会出现负数
     * 可领取张数
     * @param $receivedCount
     * @return int
     */
    public function availableCount($receivedCount)
    {
        if ($this->get_max == self::NO_LIMIT) {
            return 999;
        }
        return max($this->get_max - $receivedCount,0);
    }

    /**
     * todo 应在优惠券表添加这个字段
     * 获取已领取数量
     * @return int
     */
    public function getReceiveCount()
    {
        return $this->hasManyMemberCoupon()->count();
    }

    /**
     * 是否可领取
     * @return bool
     */
    public function available()
    {
        return $this->status == 1 && $this->get_type == 1 && ($this->total == -1 || $this->total > 0);
    }
}
