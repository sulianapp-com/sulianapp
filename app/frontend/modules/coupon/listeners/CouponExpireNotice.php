<?php

namespace app\frontend\modules\coupon\listeners;

use app\common\facades\Setting;
use app\common\models\Coupon;
use app\common\models\Member;
use app\common\models\MemberCoupon;
use app\common\models\notice\MessageTemp;
use app\common\models\UniAccount;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/12
 * Time: 下午4:28
 */
class CouponExpireNotice
{
    public $set;
    public $setLog;
    public $uniacid;

    public function handle()
    {
        \Log::info('优惠券到期处理');
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->uniacid = $u->uniacid;
            $this->set = Setting::get('shop.coupon');
            $this->setLog = Setting::get('shop.coupon_log');
            $this->sendExpireNotice();

        }
    }

    public function sendExpireNotice()
    {
        if ($this->set['every_day'] != date('H')) {
            return;
        }
        if ($this->setLog['current_d'] == date('d')) {
            return;
        }
        $this->setLog['current_d'] = date('d');
        Setting::set('shop.coupon_log', $this->setLog);

        $memberCoupons = MemberCoupon::getExpireCoupon($this->set)->get();

        foreach ($memberCoupons as $memberCoupon) {
            if ($memberCoupon->time_end == '不限时间') {
                continue;
            }
            $present = time();
            $end = strtotime(date('Y-m-d H:i:s', strtotime($memberCoupon->time_end) - $this->set['delayed'] * 86400));
            if ($present < $end || strtotime($memberCoupon->time_end) < $present) {
                continue;
            }
            $member = Member::getMemberByUid($memberCoupon->uid)->with('hasOneFans')->first();
            $couponData = [
                'name' => $memberCoupon->belongsToCoupon->name,
                'api_limit' => $this->apiLimit($memberCoupon->belongsToCoupon),
                'time_end' => $memberCoupon->time_end
            ];
            $this->sendNotice($couponData, $member);
        }
    }

    public function sendNotice($ouponData, $member)
    {
        if ($member->hasOneFans->follow == 1) {
//            $message = $this->set['expire'];
//            $message = str_replace('[优惠券名称]', $ouponData['name'], $message);
//            $message = str_replace('[优惠券使用范围]', $ouponData['api_limit'], $message);
//            $message = str_replace('[过期时间]', $ouponData['time_end'], $message);
//            $msg = [
//                "first" => '您好',
//                "keyword1" => $this->set['expire_title'] ? $this->set['expire_title'] : '优惠券过期提醒',
//                "keyword2" => $message,
//                "remark" => "",
//            ];
//            \app\common\services\MessageService::notice($this->set['template_id'], $msg, $member['openid'], $this->uniacid);
            $temp_id = $this->set['expire'];
            if (!$temp_id) {
                return;
            }
            $params = [
                ['name' => '优惠券名称', 'value' => $ouponData['name']],
                ['name' => '昵称', 'value' => $member['nickname']],
                ['name' => '优惠券使用范围', 'value' => $ouponData['api_limit']],
                ['name' => '过期时间', 'value' => $ouponData['time_end']],
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }
            \app\common\services\MessageService::notice(MessageTemp::$template_id, $msg, $member->uid, $this->uniacid);
        }
        return;
    }

    public function apiLimit($coupon)
    {
        $api_limit = '';
        switch ($coupon->use_type) {
            case Coupon::COUPON_SHOP_USE:
                $api_limit = '商城通用';
                break;
            case Coupon::COUPON_CATEGORY_USE:
                $api_limit = '适用于下列分类: ';
                $api_limit .= implode(',', $coupon['categorynames']);
                break;
            case Coupon::COUPON_GOODS_USE:
                $api_limit = '适用于下列商品: ';
                $api_limit .= implode(',', $coupon['goods_names']);
                break;
            case Coupon::COUPON_STORE_USE:
                $api_limit = '适用于下列门店: ';
                $api_limit .= implode(',', $coupon['storenames']);
                break;
            case Coupon::COUPON_SINGLE_STORE_USE:
                $api_limit = '适用于下列门店: ';
                $api_limit .= implode(',', $coupon['storenames']);
                break;
        }
        return $api_limit;
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Coupon-expire-notice', '*/10 * * * * *', function () {
                $this->handle();
                return;
            });
        });
    }
}