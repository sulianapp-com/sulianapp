<?php

namespace app\frontend\modules\coupon\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\common\modules\coupon\models\PreMemberCoupon;
use app\frontend\models\Member;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;
use app\common\models\MemberShopInfo;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use Yunshop\Hotel\common\models\CouponHotel;


class MemberCouponController extends ApiController
{
    //"优惠券中心"的优惠券
    const IS_AVAILABLE = 1; //可领取
    const ALREADY_GOT = 2; //已经领取
    const EXHAUST = 3; //已经被抢光

    //"个人拥有的优惠券"的状态
    const NOT_USED = 1; //未使用
    const OVERDUE = 2; //优惠券已经过期
    const IS_USED = 3; //已经使用

    const NO_LIMIT = -1; //没有限制 (比如对会员等级没有限制, 对领取总数没有限制)

    const TEMPLATEID = 'OPENTM200605630'; //成功发放优惠券时, 发送的模板消息的 ID

//    const TEMPLATEID = 'tqsXWjFgDGrlUmiOy0ci6VmVtjYxR7s-4BWtJX6jgeQ'; //临时调试用


    public function couponsOfMemberByStatusV2()
    {
        $status = \YunShop::request()->get('status_request');
        $uid = \YunShop::app()->getMemberId();

        $now = strtotime('now');
        $coupons = [];
        switch ($status) {
            case self::NOT_USED:
                $coupons = self::getAvailableCoupons($uid, $now);
                break;
            case self::OVERDUE:
                $coupons = self::getOverdueCoupons($uid, $now);
                break;
            case self::IS_USED:
                $coupons = self::getUsedCoupons($uid);
                break;
        }

        $data = [
            'set' => [
                'transfer' => Setting::get('coupon.transfer') ? true : false,
            ],
            'data' => $coupons,
        ];
        return $this->successJson('ok', $data);
    }

    /**
     * 获取用户所拥有的优惠券的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsOfMember()
    {
        $uid = \YunShop::app()->getMemberId();
        $pageSize = \YunShop::request()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;

        $coupons = MemberCoupon::getCouponsOfMember($uid)->paginate($pageSize)->toArray();
        if (empty($coupons['data'])) {
            return $this->errorJson('没有找到记录', []);
        }

        //添加 "是否可用" & "是否已经使用" & "是否过期" 的标识
        $now = strtotime('now');
        foreach ($coupons['data'] as $k => $v) {
            if ($v['used'] == MemberCoupon::USED) { //已使用
                $coupons['data'][$k]['api_status'] = self::IS_USED;
            } elseif ($v['used'] == MemberCoupon::NOT_USED) { //未使用
                if ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_SINCE_RECEIVE) { //时间限制类型是"领取后几天有效"
                    $end = strtotime($v['get_time']) + $v['belongs_to_coupon']['time_days'] * 3600;
                    if ($now < $end) { //优惠券在有效期内
                        $coupons['data'][$k]['api_status'] = self::NOT_USED;
                        $coupons['data'][$k]['start'] = substr($v['get_time'], 0, 10); //前端需要起止时间
                        $coupons['data'][$k]['end'] = date('Y-m-d', $end); //前端需要起止时间
                    } else { //优惠券在有效期外
                        $coupons['data'][$k]['api_status'] = self::OVERDUE;
                    }
                } elseif ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_DATE_TIME_RANGE) { //时间限制类型是"时间范围"
                    if (($now > $v['belongs_to_coupon']['time_end'])) { //优惠券在有效期外
                        $coupons['data'][$k]['api_status'] = self::OVERDUE;
                        $coupons['data'][$k]['start'] = $coupons['data'][$k]['time_start']; //为了和前面保持一致
                        $coupons['data'][$k]['end'] = $coupons['data'][$k]['time_end']; //为了和前面保持一致
                    } else { //优惠券在有效期内
                        $coupons['data'][$k]['api_status'] = self::NOT_USED;
                    }
                }
            } else {
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
            }
        }
        return $this->successJson('ok', $coupons);
    }

    /**
     * 提供给用户的"优惠券中心"的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsForMember()
    {
        $pageSize = \YunShop::request()->get('pagesize');
        $pageSize = $pageSize ? $pageSize : 10;
        $uid = \YunShop::app()->getMemberId();
        $member = MemberShopInfo::getMemberShopInfo($uid);
        if (empty($member)) {
            return $this->errorJson('没有找到该用户', []);
        }
        $memberLevel = $member->level_id;

        $now = strtotime('now');
        $coupons = Coupon::getCouponsForMember($uid, $memberLevel, null, $now)
            ->orderBy('yz_coupon.display_order', 'desc')
            ->orderBy('yz_coupon.updated_at', 'desc');
        if ($coupons->get()->isEmpty()) {
            return $this->errorJson('没有找到记录', []);
        }
        $coupons = $coupons->paginate($pageSize)->toArray();

        //添加"是否可领取" & "是否已抢光" & "是否已领取"的标识
        $couponsData = self::getCouponData($coupons, $memberLevel);

        return $this->successJson('ok', $couponsData);
    }

    /**
     * 提供给店铺装修的"优惠券中心"的数据接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function couponsForDesigner($request, $integrated = null)
    {
        $uid = \YunShop::app()->getMemberId();
        $member = MemberShopInfo::getMemberShopInfo($uid);
        if (empty($member)) {
            if(is_null($integrated)){
                return $this->errorJson('没有找到该用户', []);
            }else{
                return show_json(0,'没有找到该用户');
            }
        }
        $memberLevel = $member->level_id;

        $now = strtotime('now');
        $coupons = Coupon::getCouponsForMember($uid, $memberLevel, null, $now)
            ->orderBy('display_order', 'desc')
            ->orderBy('updated_at', 'desc');
        if ($coupons->get()->isEmpty()) {
            if(is_null($integrated)){
                return $this->errorJson('没有找到记录', []);
            }else{
                return show_json(0,'没有找到记录');
            }
        }
        $coupons_data['data'] = $coupons->get()->toArray();

        //添加"是否可领取" & "是否已抢光" & "是否已领取"的标识
        foreach ($coupons_data['data'] as $k => $v) {
            $coupons_data['data'][$k]['coupon_id'] = $coupons_data['data'][$k]['id'];
            if (($v['total'] != self::NO_LIMIT) && ($v['has_many_member_coupon_count'] >= $v['total'])) {
                $coupons_data['data'][$k]['api_availability'] = self::EXHAUST;
            } elseif ($v['member_got_count'] > 0) {
                $coupons_data['data'][$k]['api_availability'] = self::ALREADY_GOT;
            } else {
                $coupons_data['data'][$k]['api_availability'] = self::IS_AVAILABLE;
            }

            //增加属性 - 对于该优惠券,用户可领取的数量
            if ($v['get_max'] != self::NO_LIMIT) {
                $coupons_data['data'][$k]['api_remaining'] = $v['get_max'] - $v['member_got_count'];
                if ($coupons_data['data'][$k]['api_remaining'] < 0) { //考虑到优惠券设置会变更,比如原来允许领取6张,之后修改为3张,那么可领取张数可能会变成负数
                    $coupons_data['data'][$k]['api_remaining'] = 0;
                }
            } elseif ($v['get_max'] == self::NO_LIMIT) {
                $coupons_data['data'][$k]['api_remaining'] = -1;
            }
           
            //添加优惠券使用范围描述
            switch ($v['use_type']) {

                case Coupon::COUPON_SHOP_USE:
                    $coupons_data['data'][$k]['api_limit'] = '商城通用';
                    break;
                case Coupon::COUPON_CATEGORY_USE:
                    $coupons_data['data'][$k]['api_limit'] = '适用于下列分类: ';
                    $coupons_data['data'][$k]['api_limit'] = implode(',', $v['categorynames']);
                    break;
                case Coupon::COUPON_GOODS_USE:
                    $coupons_data['data'][$k]['api_limit'] = '适用于下列商品: ';
                    $coupons_data['data'][$k]['api_limit'] = implode(',', $v['goods_names']);
                    break;
                case 8:
                    $coupons_data['data'][$k]['api_limit'] = '适用于下列商品: ';
                    $coupons_data['data'][$k]['api_limit'] = implode(',', $v['goods_names']);
                    break;
            }
        }
        if(is_null($integrated)){
            return $this->successJson('ok', $coupons_data);
        }else{
            return show_json(1,$coupons_data);
        }
    }

    //添加"是否可领取" & "是否已抢光" & "是否已领取"的标识
    public static function getCouponData($coupons, $memberLevel)
    {
        foreach ($coupons['data'] as $k => $v) {
            if (($v['total'] != self::NO_LIMIT) && ($v['has_many_member_coupon_count'] >= $v['total'])) {
                $coupons['data'][$k]['api_availability'] = self::EXHAUST;
            } elseif ($v['member_got_count'] > 0) {
                $coupons['data'][$k]['api_availability'] = self::ALREADY_GOT;
            } else {
                $coupons['data'][$k]['api_availability'] = self::IS_AVAILABLE;
            }

            //增加属性 - 对于该优惠券,用户可领取的数量
            if ($v['get_max'] != self::NO_LIMIT) {
                $coupons['data'][$k]['api_remaining'] = $v['get_max'] - $v['member_got_count'];
                if ($coupons['data'][$k]['api_remaining'] < 0) { //考虑到优惠券设置会变更,比如原来允许领取6张,之后修改为3张,那么可领取张数可能会变成负数
                    $coupons['data'][$k]['api_remaining'] = 0;
                }
            } elseif ($v['get_max'] == self::NO_LIMIT) {
                $coupons['data'][$k]['api_remaining'] = -1;
            }

            //添加优惠券使用范围描述
            switch ($v['use_type']) {
                case Coupon::COUPON_SHOP_USE:
                    $coupons['data'][$k]['api_limit'] = '商城通用';
                    break;
                case Coupon::COUPON_CATEGORY_USE:
                    $coupons['data'][$k]['api_limit'] = '适用于下列分类: ';
                    $coupons['data'][$k]['api_limit'] = implode(',', $v['categorynames']);
                    break;
                case Coupon::COUPON_GOODS_USE:
                    $coupons['data'][$k]['api_limit'] = '适用于下列商品: ';
                    $coupons['data'][$k]['api_limit'] = implode(',', $v['goods_names']);
                    break;
                case 8:
                    $coupons['data'][$k]['api_limit'] = '适用于下列商品: ';
                    $coupons['data'][$k]['api_limit'] = implode(',', $v['goods_names']);
                    break;
            }
        }
        return $coupons;
    }

    //获取用户所拥有的不同状态的优惠券 - 待使用(NOT_USED) & 已过期(OVERDUE) & 已使用(IS_USED)
    public function couponsOfMemberByStatus()
    {
        $status = \YunShop::request()->get('status_request');
        $uid = \YunShop::app()->getMemberId();

        $now = strtotime('now');
        switch ($status) {
            case self::NOT_USED:
                $coupons = self::getAvailableCoupons($uid, $now);
                break;
            case self::OVERDUE:
                $coupons = self::getOverdueCoupons($uid, $now);
                break;
            case self::IS_USED:
                $coupons = self::getUsedCoupons($uid);
                break;
        }

        if (empty($coupons)) {
            return $this->errorJson('没有找到记录', []);
        } else {
            return $this->successJson('ok', $coupons);
        }
    }

    //用户所拥有的可使用的优惠券
    public static function getAvailableCoupons($uid, $time)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 0)->where('is_member_deleted', 0)->get()->toArray();

        $availableCoupons = array();
        foreach ($coupons as $k => $v) {
            $coupons[$k]['belongs_to_coupon']['deduct'] = intval($coupons[$k]['belongs_to_coupon']['deduct']);
            $coupons[$k]['belongs_to_coupon']['discount'] = intval($coupons[$k]['belongs_to_coupon']['discount']);

            if(app('plugins')->isEnabled('hotel')){
                if($v['belongs_to_coupon']['use_type'] == Coupon::COUPON_ONE_HOTEL_USE){
                    $find = CouponHotel::where('coupon_id',$v['belongs_to_coupon']['id'])->first();
                    $coupons[$k]['belongs_to_coupon']['hotel_ids'] = $find->hotel_id;
                }elseif ($v['belongs_to_coupon']['use_type'] == Coupon::COUPON_MORE_HOTEL_USE){
                    $finds = CouponHotel::where('coupon_id',$v['belongs_to_coupon']['id'])->get();
                    $findsArr = [];
                    foreach ($finds as $find_v){
                        $findsArr[] = $find_v->hotel_id;
                    }
                    $coupons[$k]['belongs_to_coupon']['hotel_ids'] = $findsArr;
                }
            }

            if ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_SINCE_RECEIVE && ($v['belongs_to_coupon']['time_days'] == 0)) { //不限时
                $coupons[$k]['belongs_to_coupon']['start'] = substr($v['get_time'], 0, 10);
                $coupons[$k]['belongs_to_coupon']['end'] = '不限时间';
                $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon'])); //增加属性 - 优惠券的适用范围
                $availableCoupons[] = array_merge($coupons[$k], $usageLimit);
            } elseif ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_SINCE_RECEIVE
                && ($time < Carbon::createFromTimestamp(strtotime($v['get_time']) + $v['belongs_to_coupon']['time_days'] * 3600 * 24)->endOfDay()->timestamp)) {
                $coupons[$k]['belongs_to_coupon']['start'] = substr($v['get_time'], 0, 10); //前端需要统一的起止时间
                $coupons[$k]['belongs_to_coupon']['end'] = date('Y-m-d', (strtotime($v['get_time']) + $v['belongs_to_coupon']['time_days'] * 3600 * 24)); //前端需要统一的起止时间
                $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon'])); //增加属性 - 优惠券的适用范围
                $availableCoupons[] = array_merge($coupons[$k], $usageLimit);
            } elseif ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_DATE_TIME_RANGE
                && $time < strtotime($v['belongs_to_coupon']['time_end'])) {
                $coupons[$k]['belongs_to_coupon']['start'] = substr($v['belongs_to_coupon']['time_start'], 0, 10); //前端需要统一的起止时间
                $coupons[$k]['belongs_to_coupon']['end'] = substr($v['belongs_to_coupon']['time_end'], 0, 10); //前端需要统一的起止时间
                $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon'])); //增加属性 - 优惠券的适用范围
                $availableCoupons[] = array_merge($coupons[$k], $usageLimit);
            }
        }
        return $availableCoupons;
    }

    //用户所拥有的已过期的优惠券
    public static function getOverdueCoupons($uid, $time)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 0)->where('is_member_deleted', 0)->get()->toArray();

        $overdueCoupons = array();
        //获取已经过期的优惠券
        foreach ($coupons as $k => $v) {
            $coupons[$k]['belongs_to_coupon']['deduct'] = intval($coupons[$k]['belongs_to_coupon']['deduct']);
            $coupons[$k]['belongs_to_coupon']['discount'] = intval($coupons[$k]['belongs_to_coupon']['discount']);

            if(app('plugins')->isEnabled('hotel')){
                if($v['belongs_to_coupon']['use_type'] == Coupon::COUPON_ONE_HOTEL_USE){
                    $find = CouponHotel::where('coupon_id',$v['belongs_to_coupon']['id'])->first();
                    $coupons[$k]['belongs_to_coupon']['hotel_ids'] = $find->hotel_id;
                }elseif ($v['belongs_to_coupon']['use_type'] == Coupon::COUPON_MORE_HOTEL_USE){
                    $finds = CouponHotel::where('coupon_id',$v['belongs_to_coupon']['id'])->get();
                    $findsArr = [];
                    foreach ($finds as $find_v){
                        $findsArr[] = $find_v->hotel_id;
                    }
                    $coupons[$k]['belongs_to_coupon']['hotel_ids'] = $findsArr;
                }
            }

            if ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_SINCE_RECEIVE
                && ($v['belongs_to_coupon']['time_days'] !== 0)
                && ($time > Carbon::createFromTimestamp(strtotime($v['get_time']) + $v['belongs_to_coupon']['time_days'] * 3600 * 24)->endOfDay()->timestamp)) {
                $coupons[$k]['belongs_to_coupon']['start'] = substr($v['get_time'], 0, 10); //前端需要统一的起止时间
                $coupons[$k]['belongs_to_coupon']['end'] = date('Y-m-d', (strtotime($v['get_time']) + $v['belongs_to_coupon']['time_days'] * 3600 * 24)); //前端需要统一的起止时间
                $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon'])); //增加属性 - 优惠券的适用范围
                $overdueCoupons[] = array_merge($coupons[$k], $usageLimit);
            } elseif ($v['belongs_to_coupon']['time_limit'] == Coupon::COUPON_DATE_TIME_RANGE
                && $time > strtotime($v['belongs_to_coupon']['time_end'])) {
                $coupons[$k]['belongs_to_coupon']['start'] = substr($v['belongs_to_coupon']['time_start'], 0, 10); //前端需要统一的起止时间
                $coupons[$k]['belongs_to_coupon']['end'] = substr($v['belongs_to_coupon']['time_end'], 0, 10); //前端需要统一的起止时间
                $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon'])); //增加属性 - 优惠券的适用范围
                $overdueCoupons[] = array_merge($coupons[$k], $usageLimit);
            }
        }
        return $overdueCoupons;
    }

    //用户所拥有的已使用的优惠券
    public static function getUsedCoupons($uid)
    {
        $coupons = MemberCoupon::getCouponsOfMember($uid)->where('used', '=', 1)->where('is_member_deleted', 0)->get()->toArray();
        $usedCoupons = array();
        //增加属性 - 优惠券的适用范围
        foreach ($coupons as $k => $v) {
            $coupons[$k]['belongs_to_coupon']['deduct'] = intval($coupons[$k]['belongs_to_coupon']['deduct']);
            $coupons[$k]['belongs_to_coupon']['discount'] = intval($coupons[$k]['belongs_to_coupon']['discount']);
            if(app('plugins')->isEnabled('hotel')){
                if($v['belongs_to_coupon']['use_type'] == Coupon::COUPON_ONE_HOTEL_USE){
                    $find = CouponHotel::where('coupon_id',$v['belongs_to_coupon']['id'])->first();
                    $coupons[$k]['belongs_to_coupon']['hotel_ids'] = $find->hotel_id;
                }elseif ($v['belongs_to_coupon']['use_type'] == Coupon::COUPON_MORE_HOTEL_USE){
                    $finds = CouponHotel::where('coupon_id',$v['belongs_to_coupon']['id'])->get();
                    $findsArr = [];
                    foreach ($finds as $find_v){
                        $findsArr[] = $find_v->hotel_id;
                    }
                    $coupons[$k]['belongs_to_coupon']['hotel_ids'] = $findsArr;
                }
            }
            $usageLimit = array('api_limit' => self::usageLimitDescription($v['belongs_to_coupon']));
            $usedCoupons[] = array_merge($coupons[$k], $usageLimit);
        }
        return $usedCoupons;
    }

    /**
     * @param $couponInArrayFormat array
     * @return string 优惠券适用范围的描述
     */
    public static function usageLimitDescription($couponInArrayFormat)
    {
        switch ($couponInArrayFormat['use_type']) {
            case 0:
                return ('商城通用');
                break;
            case 1:
                $res = '适用于下列分类: ';
                $res .= implode(',', $couponInArrayFormat['categorynames']);
                return $res;
                break;
            case 2:
                $res = '适用于下列商品: ';
                $res .= implode(',', $couponInArrayFormat['goods_names']);
                return $res;
                break;
            case 3:
                $res = '适用于下列供应商: ';
                $res .= implode(',', $couponInArrayFormat['suppliernames']);
                return $res;
                break;
            case 4:
            case 5:
                $res = '适用于下列门店: ';
                $res .= implode(',', $couponInArrayFormat['storenames']);
                return $res;
                break;
            case Coupon::COUPON_ONE_HOTEL_USE:
                $res = '适用于酒店 :';
                if(app('plugins')->isEnabled('hotel')){
                    $coupon_hotel = CouponHotel::where('coupon_id',$couponInArrayFormat['id'])->with(['hotel' => function ($query){
                        $query->select('hotel_name');
                    }])->first();
                    $res .= $coupon_hotel->hotel->hotel_name;
                }
                return $res;
                break;
            case Coupon::COUPON_MORE_HOTEL_USE:
                $res = '适用于下列酒店: ';
                if(app('plugins')->isEnabled('hotel')){
                    $hotel_arr = [];
                    $coupon_hotels = CouponHotel::where('coupon_id',$couponInArrayFormat['id'])->with(['hotel' => function ($query){
                        $query->select('hotel_name');
                    }])->get();
                    foreach ($coupon_hotels as $v){
                        $hotel_arr[] = $v->hotel->hotel_name;
                    }
                    $res .= implode(',', $hotel_arr);
                }
                return $res;
                break;
            case 8:
                $res = '适用于下列商品: ';
                $res .= implode(',', $couponInArrayFormat['goods_names']);
                return $res;
                break;
            default:
                return ('Enjoy shopping');
        }
    }

    //用户删除其拥有的优惠券
    public function delete()
    {
        $id = \YunShop::request()->id;
        if (empty($id)) {
            return $this->errorJson('缺少 ID 参数', '');
        }

        $model = MemberCoupon::find($id);
        if (!$model) {
            return $this->errorJson('找不到记录', '');
        }

        $res = $model->update(['is_member_deleted' => 1]);
        if ($res) {
            return $this->successJson('ok', '');
        } else {
            return $this->errorJson('删除优惠券失败', '');
        }
    }

    /**
     * 在"优惠券中心"点击领取优惠券
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function getCoupon()
    {
        $this->validate([
            'coupon_id' => 'required|integer'

        ]);
        $memberId = \YunShop::app()->getMemberId();

        $couponId = request('coupon_id');
        /**
         * @var $couponModel Coupon
         */
        $couponModel = Coupon::find($couponId);

        $memberCoupon = (new PreMemberCoupon);
        $memberCoupon->init(Member::current(), $couponModel);
        $memberCoupon->generate();

        $member = Member::current()->yzMember;
        //按前端要求, 需要返回和 couponsForMember() 方法完全一致的数据
        $coupon = Coupon::getCouponsForMember($memberId, $member->level_id, $couponId)->get()->toArray();
        $res = self::getCouponData(['data' => $coupon], $member->level_id);
        $res['data'][0]['coupon_id'] = $res['data'][0]['id'];
        return $this->successJson('ok', $res['data'][0]);
    }


}
