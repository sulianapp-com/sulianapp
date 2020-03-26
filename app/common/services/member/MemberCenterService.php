<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/16
 * Time: 14:41
 */

namespace app\common\services\member;

use app\common\facades\Setting;
use app\common\services\popularize\PortType;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;
use Yunshop\AlipayOnekeyLogin\services\SynchronousUserInfo;
use Yunshop\Designer\models\ViewSet;
use Yunshop\Kingtimes\common\models\Distributor;
use Yunshop\Kingtimes\common\models\Provider;


class MemberCenterService
{
   public function getMemberData($memberId)
   {
       $filter = [
           'conference',
           //'store-cashier',
           'recharge-code'
       ];

       $diyarr = [
           'tool'         => ['separate','elive','member_code','member_pay_code'],
           'asset_equity' => ['integral', 'credit', 'asset', 'love', 'coin','froze','extension', 'dragon_deposit'],
           'merchant'     => ['supplier', 'kingtimes', 'hotel', 'store-cashier', 'cashier', 'micro', 'delivery_station', 'service_station', 'voice-goods', 'staging_buy_car', 'package_deliver'],
           'market'       => [
               'ranking', 'article', 'clock_in', 'conference', 'video_demand', 'enter_goods',
               'universal_card', 'recharge_code', 'my-friend', 'business_card', 'net_car',
               'fight_groups', 'material-center', 'help-center', 'sign', 'courier',
               'declaration', 'distribution-order', 'video-share', 'pending_order', 'exchange','micro-communities','bonus-pool'
           ]

       ];

       $data = [];

       collect(app('plugins')->getPlugins())->filter(function ($item) use ($filter) {

           if (1 == $item->isEnabled()) {
               $info = $item->toArray();

               if (in_array($info['name'], $filter)) {
                   return $item;
               }
           }
       })->each(function ($item) use (&$data) {
           $info = $item->toArray();

           $name = $info['name'];
           //todo 门店暂时不传

           if ($info['name'] == "store-cashier") {
               $name = 'store_cashier';
           } elseif ($info['name'] == 'recharge-code') {
               $name = 'recharge_code';
               $class = 'icon-member-recharge1';
               $url = 'rechargeCode';
               $image = 'member_a(3).png';
           } elseif ($info['name'] == 'conference') {
               $name = 'conference';
               $class = 'icon-member-act-signup1';
               $url = 'conferenceList';
               $image = 'member_a(15).png';
           }

           $data[] = [
               'name'  => $name,
               'title' => $info['title'],
               'class' => $class,
               'url'   => $url,
               'image' => $image
           ];
       });
       if (app('plugins')->isEnabled('asset') && (new \Yunshop\Asset\Common\Services\IncomeDigitizationService)->memberPermission()) {
           $data[] = [
               'name'  => 'asset',
               'title' => PLUGIN_ASSET_NAME,
               'class' => 'icon-number_assets',
               'url'   => 'TransHome',
               'image' => 'member_a(69).png'
           ];
       }

       if(\Setting::getByGroup('coupon')['exchange_center'] == 1){
           $data[] = [
               'name' => 'exchange',
               'title' => '兑换中心',
               'class' => 'icon-member_changer_centre',
               'url' => 'CouponExchange',
               'image' => 'member_a(74).png',
           ];
       }

       if (PortType::popularizeShow(\YunShop::request()->type)) {
           $data[] = [
               'name' => 'extension',
               'title' => '推广中心',
               'class' => 'icon-member-extension1',
               'url' => 'extension',
               'image' => 'member_a(38).png'
           ];
       }

       if (app('plugins')->isEnabled('business-card')) {
           $is_open = Setting::get('business-card.is_open');
           if ($is_open == 1) {
               $data[] = [
                   'name'  => 'business_card',
                   'title' => '名片',
                   'class' => 'icon-member_card1',
                   'url'   => 'CardCenter',
                   'image' => 'member_a(58).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('declaration')) {
           if(Setting::get('plugin.declaration.switch')){
               $data[] = [
                   'name'  => 'declaration',
                   'title' => DECLARATION_NAME,
                   'class' => 'icon-declaration_system',
                   'url'   => 'DeclarationApply',
                   'image' => 'member_a (66).png'
               ];
           }
       }


       //配送站
       if (app('plugins')->isEnabled('delivery-station')) {
           $delivery_station_setting = Setting::get('plugin.delivery_station');
           $delivery_station = \Yunshop\DeliveryStation\models\DeliveryStation::memberId($memberId)->first();
           if ($delivery_station && $delivery_station_setting['is_open']) {
               $data[] = [
                   'name'  => 'delivery_station',
                   'title' => '配送站',
                   'class' => 'icon-delivery_order',
                   'url'   => 'deliveryStation',
               ];
           }
       }
       //服务站
       if (app('plugins')->isEnabled('service-station')) {
           $service_station = \Yunshop\ServiceStation\models\ServiceStation::isBlack()->memberId($memberId)->first();
           if ($service_station) {
               $data[] = [
                   'name' => 'service_station',
                   'title' => '服务站',
                   'class' => 'icon-service_station',
                   'url' => 'serviceStation',
               ];
           }
       }

       if (app('plugins')->isEnabled('material-center')) {
           $data[] = [
               'name'  => 'material-center',
               'title' => '素材中心',
               'class' => 'icon-member_material',
               'url'   => 'materialCenter',
               'image' => 'member_a(65).png'
           ];
       }


       if (app('plugins')->isEnabled('distribution-order')) {
           $disorder_setting = Setting::get('plugins.distribution-order');
           if ($disorder_setting && 1 == $disorder_setting['is_open']) {
               $data[] = [
                   'name'  => 'distribution-order',
                   'title' => $disorder_setting['title'] ? : '分销订单统计',
                   'class' => 'icon-order_system',
                   'url'   => 'DistributionOrders',
                   'image' => 'member_a(70).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('credit')) {
           $credit_setting = Setting::get('plugin.credit');
           if ($credit_setting && 1 == $credit_setting['is_credit']) {
               $data[] = [
                   'name'  => 'credit',
                   'title' => '信用值',
                   'class' => 'icon-member-credit01',
                   'url'   => 'creditInfo',
                   'image' => 'member_a(44).png'
               ];
           }
       }
       if (app('plugins')->isEnabled('ranking')) {
           $ranking_setting = Setting::get('plugin.ranking');
           if ($ranking_setting && 1 == $ranking_setting['is_ranking']) {
               $data[] = [
                   'name'  => 'ranking',
                   'title' => '排行榜',
                   'class' => 'icon-member-bank-list1',
                   'url'   => 'rankingIndex',
                   'image' => 'member_a(29).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('micro')) {
           $micro_set = \Setting::get('plugin.micro');
           if ($micro_set['is_open_miceo'] == 1) {
               $micro_shop = \Yunshop\Micro\common\models\MicroShop::getMicroShopByMemberId($memberId);
               if ($micro_shop) {
                   $data[] = [
                       'name'  => 'micro',
                       'title' => MICRO_PLUGIN_NAME.'中心',
                       'class' => 'icon-member-mendian1',
                       'url'   => 'microShop_home',
                       'image' => 'member_a(40).png'
                   ];
               } else {
                   $data[] = [
                       'name'  => 'micro',
                       'title' => '我要开店',
                       'class' => 'icon-member-mendian1',
                       'url'   => 'microShop_apply',
                       'image' => 'member_a(40).png'
                   ];
               }
           }
       }

       if (app('plugins')->isEnabled('help-center')) {
           $status = \Setting::get('help-center.status') ? true : false;
           if ($status) {
               $data[] = [
                   'name'  => 'help-center',
                   'title' => '帮助中心',
                   'class' => 'icon-member-help',
                   'url'   => 'helpcenter',
                   'image' => 'member_a(2).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('love')) {
           $data[] = [
               'name' => 'love',
               'title' => \Yunshop\Love\Common\Services\SetService::getLoveName() ?: '爱心值',
               'class' => 'icon-member-exchange1',
               'url' => 'love_index',
               'image' => 'member_a(1).png'
           ];
       }

       if (app('plugins')->isEnabled('froze')) {
           $data[] = [
               'name' => 'froze',
               'title' => \Yunshop\Froze\Common\Services\SetService::getFrozeName() ?: '冻结币',
               'class' => 'icon-member-frozen1',
               'url' => 'FrozenCoin',
               'image' => 'member_a(7).png'
           ];
       }

       if (app('plugins')->isEnabled('coin')) {
           $data[] = [
               'name' => 'coin',
               'title' => \Yunshop\Coin\Common\Services\SetService::getCoinName() ?: '华侨币',
               'class' => 'icon-member-currency1',
               'url' => 'overseas_index',
               'image' => 'member_a(14).png'
           ];
       }

       if (app('plugins')->isEnabled('elive')) {
           $data[] = [
               'name' => 'elive',
               'title' => '生活缴费',
               'class' => 'icon-shenghuojiaofei',
               'url' => 'lifeService',
               'image'=>'member_a(49).png'
           ];
       }

       if (app('plugins')->isEnabled('face-payment')) {
           $data[] = [
               'name' => 'member_code',
               'title' => '会员卡号 ',
               'class' => 'icon-member_posvip_cardnum',
               'url' => 'uidCode',
               'image'=>'member_a(83).png'
           ];
           $data[] = [
               'name' => 'member_pay_code',
               'title' => '动态验证码',
               'class' => 'icon-member_pospay_validation',
               'url' => 'codePage',
               'image'=>'member_a(82).png'
           ];
       }

       if (app('plugins')->isEnabled('sign')) {
           $data[] = [
               'name' => 'sign',
               'title' => trans('Yunshop\Sign::sign.plugin_name') ?: '签到',
               'class' => 'icon-member-clock1',
               'url' => 'sign',
               'image' => 'member_a(30).png'
           ];
       }

       if (app('plugins')->isEnabled('courier')) {
           //快递单
           $status = \Setting::get('courier.courier.radio');
           if ($status) {
               $data[] = [
                   'name' => 'courier',
                   'title' => '快递',
                   'image' => 'member_a(68).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('voice-goods'))
       {
           $set = \Setting::get('plugin.voice-goods');
           if($set['is_open_voice'] == 1)
           {
               $data[] = [
                   'name'  => 'voice-goods',
                   'title' => $set['voice_name'],
                   'class' => 'icon-member_voice_center',
                   'url'   => 'MyVoices',
                   'image' => 'member_a(75).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('my-friend')) {
           $data[] = [
               'name'  => 'my-friend',
               'title' => MY_FRIEND_NAME,
               'class' => 'icon-member_my-friend',
               'url'   => 'MyFriendApply',
               'image' => 'member_a(63).png'
           ];
       }

       if (app('plugins')->isEnabled('article')) {
           $article_setting = Setting::get('plugin.article');

           if ($article_setting) {
               $data[] = [
                   'name'  => 'article',
                   'title' => $article_setting['center'] ?: '文章中心',
                   'class' => 'icon-member-collect1',
                   'url'   => 'notice',
                   'param' => 0,
                   'image' => 'member_a(41).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('clock-in')) {
           $clockInService = new \Yunshop\ClockIn\services\ClockInService();
           $pluginName = $clockInService->get('plugin_name');

           $clock_in_setting = Setting::get('plugin.clock_in');

           if ($clock_in_setting && 1 == $clock_in_setting['is_clock_in']) {
               $data[] = [
                   'name'  => 'clock_in',
                   'title' => $pluginName,
                   'class' => 'icon-member-get-up',
                   'url'   => 'ClockPunch',
                   'image' => 'member_a(47).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('video-demand')) {

           $video_demand_setting = Setting::get('plugin.video_demand');

           if ($video_demand_setting && $video_demand_setting['is_video_demand']) {
               $data[] = [
                   'name'  => 'video_demand',
                   'title' => '课程中心',
                   'class' => 'icon-member-course3',
                   'url'   => 'CourseManage',
                   'image' => 'member_a(22).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('help-center')) {

           $help_center_setting = Setting::get('plugin.help_center');

           if ($help_center_setting && 1 == $help_center_setting['status']) {
               $data[] = [
                   'name'  => 'help_center',
                   'title' => '帮助中心',
                   'class' => 'icon-member-help',
                   'url'   => 'helpcenter'
               ];
           }
       }

       if (app('plugins')->isEnabled('store-cashier')) {
           $store = \Yunshop\StoreCashier\common\models\Store::getStoreByUid($memberId)->first();

           if (!$store) {
               $data[] = [
                   'name'  => 'store-cashier',
                   'title' => '门店申请',
                   'class' => 'icon-member-store-apply1',
                   'url'   => 'storeApply',
                   'image' => 'member_a(26).png'
               ];
           }

           if ($store && $store->is_black != 1) {
               $data[] = [
                   'name'  => 'store-cashier',
                   'title' => '门店管理',
                   'class' => 'icon-member_store',
                   'url'   => 'storeManage',
                   'image' => 'member_a(26).png'
               ];

               if ($store->hasOneCashier->hasOneCashierGoods->is_open == 1) {
                   $data[] = [
                       'name' => 'cashier',
                       'title' => '收银台',
                       'class' => 'icon-member-cashier',
                       'url' => 'cashier',
                       'api' => 'plugin.store-cashier.frontend.cashier.center.index',
                       'image' => 'member_a(43).png'
                   ];
               }
           }


       }
       if (app('plugins')->isEnabled('supplier')) {
           $supplier_setting = Setting::get('plugin.supplier');
           $supplier = \Yunshop\Supplier\common\models\Supplier::getSupplierByMemberId($memberId, 1);

           if (!$supplier) {
               $data[] = [
                   'name'  => 'supplier',
                   'title' => '供应商申请',
                   'class' => 'icon-member-apply1',
                   'url'   => 'supplier',
                   'api'   => 'plugin.supplier.supplier.controllers.apply.supplier-apply.apply',
                   'image' => 'member_a(53).png'
               ];
           } elseif ($supplier_setting && 1 == $supplier_setting['status']) {
               $data[] = [
                   'name'  => 'supplier',
                   'title' => $supplier_setting['name'] ?: '供应商管理',
                   'class' => 'icon-member-supplier',
                   'url'   => 'SupplierCenter',
                   'image' => 'member_a(53).png'
               ];
           }
       }
       if (app('plugins')->isEnabled('kingtimes')) {
           $provider = Provider::select(['id', 'uid', 'status'])->where('uid',
               $memberId)->first();
           $distributor = Distributor::select(['id', 'uid', 'status'])->where('uid',
               $memberId)->first();

           if ($provider) {

               if ($provider->status == 1) {
                   $data[] = [
                       'name'  => 'kingtimes',
                       'title' => '补货商中心',
                       'class' => 'icon-member-replenishment',
                       'url'   => 'ReplenishmentApply',
                       'image' => 'member_a(67).png'
                   ];
               }
           } else {
               $data[] = [
                   'name'  => 'kingtimes',
                   'title' => '补货商申请',
                   'class' => 'icon-member-replenishment',
                   'url'   => 'ReplenishmentApply',
                   'image' => 'member_a(67).png'
               ];
           }
           if ($distributor) {
               if ($distributor->status == 1) {
                   $data[] = [
                       'name'  => 'kingtimes',
                       'title' => '配送站中心',
                       'class' => 'icon-member-express-list',
                       'url'   => 'DeliveryTerminalApply',
                       'image' => 'member_a(54).png'
                   ];
               }
           } else {
               $data[] = [
                   'name'  => 'kingtimes',
                   'title' => '配送站申请',
                   'class' => 'icon-member-express-list',
                   'url'   => 'DeliveryTerminalApply',
                   'image' => 'member_a(54).png'
               ];
           }
           // dd($data);

       }
       if (app('plugins')->isEnabled('enter-goods')) {

           $data[] = [
               'name'  => 'enter_goods',
               'title' => '用户入驻',
               'class' => 'icon-member_goods',
               'url'   => 'EnterShop',
               'image' => 'member_a(52).png'
           ];
       }

       if (app('plugins')->isEnabled('integral')) {
           $status = \Yunshop\Integral\Common\Services\SetService::getIntegralSet();

           if ($status['member_show']) {
               $data[] = [
                   'name'  => 'integral',
                   'title' => $status['plugin_name'] ?: '消费积分',
                   'class' => 'icon-member_integral',
                   'url'   => 'Integral_love',
                   'image' => 'member_a(55).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('universal-card')) {
           $set = \Yunshop\UniversalCard\services\CommonService::getSet();
           //判断插件开关
           if ($set['switch']) {
               $shopSet = \Setting::get('shop.member');
               //判断商城升级条件是否为指定商品
               if ($shopSet['level_type'] == 2) {
                   $data[] = [
                       'name'  => 'universal_card',
                       'title' => $set['name'],
                       'class' => 'icon-card',
                       'url'   => 'CardIndex',
                       'image' => 'member_a(57).png'
                   ];
               }
           }
       }

       if (app('plugins')->isEnabled('separate')) {
           $setting = \Setting::get('plugin.separate');
           if ($setting && 1 == $setting['separate_status']) {
               $data[] = [
                   'name'  => 'separate',
                   'title' => '绑定银行卡',
                   'class' => 'icon-yinhangqia',
                   'url'   => 'BankCard',
                   'image' => 'tool_a(10).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('hotel')) {
           $hotel = \Yunshop\Hotel\common\models\Hotel::getHotelByUid($memberId)->first();
           if ($hotel) {
               $data[] = [
                   'name'  => 'hotel',
                   'title' => HOTEL_NAME . '管理',
                   'class' => 'icon-member_hotel',
                   'url'   => 'HotelManage',
                   'image' => 'member_a(56).png'
               ];
           } else {
               $data[] = [
                   'name'  => 'hotel',
                   'title' => HOTEL_NAME . '申请',
                   'class' => 'icon-member-hotel-apply',
                   'url'   => 'hotelApply',
                   'image' => 'member_a(56).png'
               ];
           }
           //酒店自定义字段
           $set = \Setting::get('plugin.hotel');
           $arr['hotel'] = [
               'hotel_home_page' => $set['hotel_home_page'] ?: '酒店主页',
               'check_the_room' => $set['check_the_room'] ?: '查看房型',
               'hotel_intro' => $set['hotel_intro'] ?: '酒店简介',
               'goods_details' => $set['goods_details'] ?: '商品详情',
               'goods_presentation' => $set['goods_presentation'] ?: '商品介绍',
               'goods_parameters' => $set['goods_parameters'] ?: '商品参数',
               'user_evaluation' => $set['user_evaluation'] ?: '用户评价',
               'hotels' => $set['hotels'] ?: '酒店',
               'hotel_first_page' => $set['hotel_first_page'] ?: '酒店首页',
               'hotel_find' => $set['hotel_find'] ?: '查找酒店',
               'hotel_find_name' => $set['hotel_find_name'] ?: '酒店名'
           ];
       }

       //网约车插件开启关闭
       if (app('plugins')->isEnabled('net-car')) {

           $video_demand_setting = Setting::get('plugin.net_car');

           if ($video_demand_setting && $video_demand_setting['net_car_open']) {
               $data[] = [
                   'name'  => 'net_car',
                   'title' => '网约车',
                   'class' => 'icon-member_my-card',
                   'url'   => 'online_car',
                   'image' => 'member_a(64).png'
               ];
           }
       }

       //拼团插件开启关闭
       if (app('plugins')->isEnabled('fight-groups')) {
           $data[] = [
               'name'  => 'fight_groups',
               'title' => '我的拼团',
               'class' => 'icon-member_mygroup',
               'url'   => 'MyGroups',
               'image' => 'member_a(72).png'
           ];
       }

       //发现视频
       if (app('plugins')->isEnabled('video-share')) {
           $set = \Setting::get('plugin.video-share');
           if ($set['is_open']) {
               $data[] = [
                   'name'  => 'video-share',
                   'title' => '发现视频',
                   'class' => 'icon-extension_video',
                   'url'   => 'VideoList',
                   'image' => 'member_a(73).png'
               ];
           }
       }


       //微社区
       if (app('plugins')->isEnabled('micro-communities')) {
           $set = \Setting::get('plugin.micro-communities');
           if ($set && $set['is_open_micro'] == 1) {
               $data[] = [
                   'name' => 'micro-communities',
                   'title' => '微社区',
                   'class' => 'icon-member_community',
                   'url' => 'MicroHome',
                   'image' => 'member_a(74).png'
               ];
           }
       }

       //商品挂单
       if (app('plugins')->isEnabled('pending-order')) {
           $set = \Setting::get('plugin.pending_order');
           if ($set['whether_open']) {
               $data[] = [
                   'name'  => 'pending_order',
                   'title' => \Yunshop\PendingOrder\services\PendingOrderService::PendingOrderName(),
                   'class' => 'icon-extension_goods_order',
                   'url'   => 'EntryVolume',
                   'image' => 'member_a(59).png'
               ];
           }
       }


       //分期购车
       if (app('plugins')->isEnabled('staging-buy-car')) {
           $set = \Setting::get('plugin.staging_buy_car');
           if ($set['staging_buy_car_open'] != '1') {
               $data[] = ['name' => 'staging_buy_car', 'title' => '分期购车', 'class' => 'icon-member_installment_buycar', 'url' => 'hireCarManage', 'image' => 'member_a(80).png'];
           }
       }
       //龙存管插件开启
       if (app('plugins')->isEnabled('dragon-deposit')) {

           $data[] = [
               'name'  => 'dragon_deposit',
               'title' => '龙存管',
               'class' => 'icon-member_construction_deposit',
               'url'   => 'myWallet',
               'image' => 'member_a(76).png'
           ];

       }

       //自提点
       if (app('plugins')->isEnabled('package-deliver')) {
           $is_package = \Setting::get('plugin.package_deliver.is_package');
           if ($is_package) {
               $data[] = [
                   'name'  => 'package_deliver',
                   'title' => '自提点申请',
                   'class' => 'icon-member_place_apply',
                   'url'   => 'SelfCarryApply',
                   'image' => 'member_a(77).png'
               ];
           }
       }

       if (app('plugins')->isEnabled('bonus-pool')) {

           $pluginName = Setting::get('plugin.bonus_pool')['bonus_pool_name'];

           $data[] = [
               'name'  => 'bonus-pool',
               'title' => $pluginName ?: '奖金池',
               'class' => 'icon-member_bonus_pools',
               'url'   => 'Bonus',
               'image' => 'member_a(81).png'
           ];

       }

       foreach ($data as $k => $v) {

           if (in_array($v['name'], $diyarr['tool'])) {
               $arr['tool'][] = $v;
           }
           if (in_array($v['name'], $diyarr['asset_equity'])) {
               $arr['asset_equity'][] = $v;
           }
           if (in_array($v['name'], $diyarr['merchant'])) {
               $arr['merchant'][] = $v;
           }
           if (in_array($v['name'], $diyarr['market'])) {
               $arr['market'][] = $v;
           }
       }

       $arr['ViewSet'] = [];
       if (app('plugins')->isEnabled('designer')) {
           //获取所有模板
           $sets = ViewSet::uniacid()->select('names', 'type')->get()->toArray();
           
           foreach ($sets as $k => $v) {
               $arr['ViewSet'][$v['type']]['name'] = $v['names'];
               $arr['ViewSet'][$v['type']]['name'] = $v['names'];
           }
       }

       $arr['is_open'] = [
           'is_open_hotel' => app('plugins')->isEnabled('hotel') ? 1 : 0,
           'is_open_net_car' => app('plugins')->isEnabled('net-car') ? 1 : 0,
           'is_open_fight_groups' => app('plugins')->isEnabled('fight-groups') ? 1 : 0,
           'is_open_lease_toy' => \app\common\services\plugin\leasetoy\LeaseToySet::whetherEnabled(), //租赁订单列表是否开启
           'is_open_converge_pay' => app('plugins')->isEnabled('converge_pay') ? 1 : 0,
           'is_store' => $store && $store->is_black != 1 ? 1 : 0,
       ];

       return $arr;
   }
}