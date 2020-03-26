<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzSettingTable extends Migration
{
    protected $table = 'yz_setting';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->table) && \YunShop::app()->uniacid) {
            // 商城
            $shop = \Setting::get('shop.shop');
            if (!$shop) {
                \Setting::set('shop.shop', [
                    'close' => 0,
                    'name' => '',
                    'logo' => '',
                    'signimg' => '',
                    'achievement' => 0,
                    'cservice' => '',
                    'copyright' => '',
                    'credit' => '余额',
                    'credit1' => ''
                ]);
            }

            // 会员
            $member = \Setting::get('shop.member');
            if (!$member) {
                \Setting::set('shop.member', [
                    'headimg' => '',
                    'level_name' => '普通会员',
                    'display_page' => 0,
                    'level_type' => 0,
                    'level_after' => 0,
                    'term' => 0,
                    'is_bind_mobile' => 0,
                    'show_balance' => 0,
                    'is_invite' => 0,
                    'required' => 0,
                    'invite_page' => 0,
                    'is_custom' => 0,
                    'custom_title' => '',
                    'form_id' => 0,
                    'get_register' => 0,
                    'Close_describe' => '',
                    'wechat_login_mode' => 0
                ]);
            }

            // 订单
            $order = \Setting::get('shop.order');
            if (!$order) {
                \Setting::set('shop.order', [
                    'paid_process' => 0,
                    'receive_process' => 0
                ]);
            }

            // 分类
            $category = \Setting::get('shop.category');
            if (!$category) {
                \Setting::set('shop.category', [
                    'cat_level' => 2,
                    'cat_adv_img' => '',
                    'cat_adv_url' => ''
                ]);
            }

            // 短信
            $sms = \Setting::get('shop.sms');
            if (!$sms) {
                \Setting::set('shop.sms', [
                    'status' => 0,
                    'type' => 1,
                    'account' => '',
                    'password' => '',
                    'account2' => '',
                    'password2' => '',
                    'appkey' => '',
                    'secret' => '',
                    'signname' => '',
                    'templateCode' => '',
                    'product' => '',
                    'templateCodeForget' => '',
                    'forget' => '',
                    'aly_appkey' => '',
                    'aly_secret' => '',
                    'aly_signname' => '',
                    'aly_templateCode' => '',
                    'aly_templateCodeForget' => ''
                ]);
            }

            // 优惠券
            $coupon = \Setting::get('shop.coupon');
            if (!$coupon) {
                \Setting::set('shop.coupon', [
                    'delayed' => '',
                    'send_times' => 0,
                    'every_day' => 0,
                    'expire' => ''
                ]);
            }

            // 会员资料表单
            $form = \Setting::get('shop.form');
            if (!$form) {
                \Setting::set('shop.form', json_encode([
                    'base' => [
                        'sex' => 0,
                        'address' => 0,
                        'birthday' => 0,
                    ],
                    'form' => []
                ]));
            }

            // 注册协议
            $protocol = \Setting::get('shop.protocol');
            if (!$protocol) {
                \Setting::set('shop.protocol', [
                    'protocol' => 0,
                    'content' => '',
                ]);
            }

            // 物流查询
            $express_info = \Setting::get('shop.express_info');
            if (!$express_info) {
                \Setting::set('shop.express_info', [
                    'KDN' => [
                        'express_api' => 1002,
                        'eBusinessID' => '',
                        'appKey' => ''
                    ]
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_setting');
    }
}
