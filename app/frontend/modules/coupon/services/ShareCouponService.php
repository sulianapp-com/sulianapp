<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/21
 * Time: 9:51
 */

namespace app\frontend\modules\coupon\services;


use app\common\exceptions\ShopException;
use app\common\models\Coupon;
use app\common\models\coupon\ShoppingShareCouponLog;
use app\common\models\MemberCoupon;
use app\frontend\modules\coupon\models\ShoppingShareCoupon;
use app\frontend\models\Member;

class ShareCouponService
{
    public static function fen($share_model)
    {
        $coupon_ids = $share_model->share_coupon;

        $key = array_rand($coupon_ids,1);

        $couponModel = Coupon::find($coupon_ids[$key]);
//dd($couponModel);
        $getTotal = MemberCoupon::uniacid()->where("coupon_id", $coupon_ids[$key])->count();

        $lastTotal = $couponModel->total - $getTotal;

        $share_log = ShoppingShareCouponLog::uniacid()->shareCouponId($share_model->id)->shareUid($share_model->member_id)->receiveUid(\YunShop::app()->getMemberId())->first();

        if ($share_log) {
            return self::toData('RT', '已领取不可重复领取');
        } elseif(!$couponModel->status) {
            return self::toData('RT', '该优惠券已下架');
        } elseif (($couponModel->total != -1) && (1 > $lastTotal)) {
            return self::toData('RT', '已经被抢光了');
        } elseif ((!$share_model->obtain_restriction) && $share_model->member_id == \YunShop::app()->getMemberId()) {
            return self::toData('RT', '分享者不可领取');
        }


        $bool =  self::sendCoupon($share_model,$couponModel,\YunShop::app()->getMemberId(),$key);


        if ($bool) {
            return self::toData('YES', '成功' ,  $couponModel->toArray());
        }
        return self::toData('ER', '数据保存失败');
    }

    protected static function sendCoupon($share_model, $couponModel, $receiveUid, $coupon_key)
    {
        //return true;

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'uid' => $receiveUid,
            'coupon_id' => $couponModel->id,
            'get_type' => 0,
            'used' => 0,
            'get_time' => strtotime('now'),
        ];


        $logData = [
            'uniacid' => \YunShop::app()->uniacid,
            'share_uid' => $share_model->member_id,
            'order_id' => $share_model->order_id,
            'share_coupon_id' => $share_model->id,
            'receive_uid' => $receiveUid,
            'coupon_id' => $couponModel->id,
            'coupon_name' => $couponModel->name,
            'remark' => $couponModel->coupon_method == 1 ? '￥'.$couponModel->deduct : $couponModel->discount.'折',
        ];

        $member_coupon = MemberCoupon::create($data);
        //写入log
        if ($member_coupon) { //发放优惠券成功
            $logData['log'] = '用户( ID 为 ' . $receiveUid . ' )通过用户( ID为 ' . $share_model->member_id . ' )的分享领取 1 张优惠卷(ID 为 ' . $couponModel->id . ' )';

        } else { //发放优惠券失败
            $logData['log'] = '分享领取失败：用户( ID 为 ' . $receiveUid . ' )通过用户( ID为 ' . $share_model->member_id . ' )的分享领取 1 张优惠卷(ID 为 ' . $couponModel->id . ' )';
            \Log::debug($logData['log']);
            return false;
        }

        ShoppingShareCouponLog::create($logData);

        $share_model->receive_coupon =  array_merge( isset($share_model->receive_coupon)?$share_model->receive_coupon: [], [$share_model->share_coupon[$coupon_key]]);

        $share_model->share_coupon = self::handleArray($share_model->share_coupon, $coupon_key, 'cut');

        $share_model->status = count($share_model->share_coupon) > 0 ?0:1;

        $bool = $share_model->save();

        if ($bool) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $coupon_ids array 优惠卷数组
     * @param $key int 数组下标
     * @param string $type 处理方式
     * @return array
     */
    protected static function handleArray($coupon_ids, $key, $type = 'cut')
    {
        array_splice($coupon_ids, $key, 1);

        return $coupon_ids;
    }

    protected static function toData($state = '999999', $error = '', $data = [])
    {
        return [
            'state' => $state,
            'msg' => $error,
            'data' => $data,
        ];
    }


    /**
     * 支付是否显示分享页
     * @param array|string $order_ids
     * @return bool
     */
    public static function showIndex($order_ids,$member_id)
    {
        if (!is_array($order_ids)) {
            if (strpos($order_ids, '_') !== false) {
                $order_ids = explode('_', rtrim($order_ids, '_'));
            } else {
                $order_ids = explode(',', rtrim($order_ids, ','));
            }
        }

        $share_model = ShoppingShareCoupon::whereIn('order_id', $order_ids)->get();
        $member = Member::with(['yzMember'])->find($member_id);

        if ($share_model->isEmpty() || (!$member)) {
            return false;
        }



        $set = \Setting::get('coupon.shopping_share');

        //拥有推广资格的会员才可以分享
        if ($set['share_limit'] == 1) {
            if (!$member->yzMember->is_agent) {
                return false;
            }
        }


        return true;
    }
}