<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/15
 * Time: 18:09
 */

namespace app\frontend\modules\refund\services;

use app\common\services\MessageService;
use app\common\facades\Setting;
use app\common\models\notice\MessageTemp;
use app\backend\modules\order\models\Order;
use app\backend\modules\member\models\Member;
use app\backend\modules\goods\models\Goods;
use app\backend\modules\order\models\OrderGoods;
use app\common\models\notice\MinAppTemplateMessage;
class RefundMessageService extends MessageService
{
    public static function applyRefundNotice($refundApply,$uniacid = '')
    {
        $couponNotice = Setting::get('shop.notice');
        $temp_id = $couponNotice['order_refund_apply'];
        if ($temp_id) {
            $memberDate = Member::getMemberBaseInfoById($refundApply->uid);
            $orderDate = Order::getOrderDetailById($refundApply->order_id);
//        $goods = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('goods_option_title');//商品详情
//        $goods_title = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('title').$goods;
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $memberDate['nickname']],
                ['name' => '退款单号', 'value' => $refundApply->refund_sn],
//            ['name' => '下单时间', 'value' => $orderDate['create_time']],
//            ['name' => '订单金额', 'value' => $orderDate['price']],
//            ['name' => '运费', 'value' => $orderDate['dispatch_price']],
//            ['name' => '商品详情（含规格）', 'value' => $goods_title],
//            ['name' => '支付方式', 'value' => $orderDate->pay_type_name],
                ['name' => '退款申请时间', 'value' => $refundApply->create_time],
                ['name' => '退款方式', 'value' => $orderDate->pay_type_name],
                ['name' => '退款金额', 'value' => $refundApply->price],
                ['name' => '退款原因', 'value' => $refundApply->reason],
            ];

            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return false;
            }
            MessageService::notice(MessageTemp::$template_id, $msg, $refundApply->uid, $uniacid);
        }


        $is_open = MinAppTemplateMessage::getTitle('退款通知');
        if (!$is_open->is_open){
            return;
        }
        $orderDate = Order::getOrderDetailById($refundApply->order_id);
        $msg = [
            'keyword1'=>['value'=>  $orderDate->pay_type_name],// 退款类型
            'keyword2'=>['value'=> $refundApply->price],//退款金额
            'keyword3'=>['value'=> date("Y-m-d H:i:s")],// 退款时间
            'keyword4'=>['value'=>  $refundApply->reason],// 退款原因
            'keyword5'=>['value'=> $refundApply->refund_sn],// 订单编号
        ];
        $news_link = MessageTemp::find($temp_id)->news_link;
        $news_link = $news_link ?:'';
        MessageService::MiniNotice($is_open->template_id,$msg,$refundApply->uid.'',$news_link);
    }

    public static function applyRefundNoticeBuyer($refundApply,$uniacid = '')
    {
        $couponNotice = Setting::get('shop.notice');
        // \Log::info('shop.notice', $couponNotice);
        //获取用户退货退款通知商家的消息模板
        $temp_id = $couponNotice['order_refund_apply_to_saler'];

        if (!$temp_id) {
            return false;
        }
        $nickname = \app\common\models\Member::where('uid', $refundApply->uid)->first()->nickname;

        $ordersn = Order::find($refundApply->order_id)->order_sn;
        $orderDate = Order::getOrderDetailById($refundApply->order_id);
        //品详情
        $goods = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('goods_option_title');
        $goods_title = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('title').$goods;

        //统计通知用户人数并整理数据
        $peonum = count($couponNotice['salers']);
        $key = range(0, $peonum-1);
        $new_perpson = array_combine($key, $couponNotice['salers']);

        if ($peonum >= 1) {
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $nickname],
                ['name' => '退款单号', 'value' => $refundApply->refund_sn],
                ['name' => '退款申请时间', 'value' => $refundApply->create_time],
                ['name' => '退款类型', 'value' => $refundApply->refund_type_name],
                ['name' => '退款方式', 'value' => $orderDate->pay_type_name],
                ['name' => '退款原因', 'value' => $refundApply->reason],
                ['name' => '订单编号', 'value' => $ordersn],
                ['name' => '退款金额', 'value' => $refundApply->price],
                ['name' => '商品详情（含规格）', 'value' => $goods_title],
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            
            foreach ($new_perpson as $k => $v) {
                
                if ($msg) {
                    //公众号通知
                    MessageService::notice(MessageTemp::$template_id, $msg, $v['uid'], $uniacid);
                }

                $is_open = MinAppTemplateMessage::getTitle('退款申请通知');
                if (!$is_open->is_open){
                    \Log::debug('暂未开启小程序退款申请通知');
                    continue;
                }
                \Log::debug('----------------小程序退款通知+++++++++++++++++');
                $msg = [
                    'keyword1'=>['value'=>  $nickname],// 退款人
                    'keyword2'=>['value'=> $refundApply->refund_sn],//退款单号
                    'keyword3'=>['value'=> $refundApply->create_time],// 退款时间
                    'keyword4'=>['value'=>  $orderDate->pay_type_name],// 退款方式
                    'keyword5'=>['value'=> $refundApply->price],// 订单金额
                    'keyword6'=>['value'=> $refundApply->reason],// 订单原因
                ];
                $news_link = MessageTemp::find($temp_id)->news_link ? : '';

                MessageService::MiniNotice($is_open->template_id, $msg, $v['uid'], $news_link);
            }

        } else {
            return false;
        }
    }
}