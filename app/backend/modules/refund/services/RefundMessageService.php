<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/14
 * Time: 15:13
 */

namespace app\backend\modules\refund\services;

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
    public static function rejectMessage($refundApply,$uniacid = '')
    {

        $couponNotice = Setting::get('shop.notice');
        $temp_id = $couponNotice['order_refund_reject'];

        $memberDate = Member::getMemberBaseInfoById($refundApply->uid);
        $orderDate = Order::getOrderDetailById($refundApply->order_id);
//        $goods = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('goods_option_title');//商品详情
//        $goods_title = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('title').$goods;
        if ($temp_id) {
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
//            ['name' => '退款驳回时间', 'value' => date('Y-m-d H:i:s', $refundApply->updated_at)],
                ['name' => '驳回原因', 'value' => $refundApply->reject_reason],
            ];

            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return false;
            }
            $news_link = MessageTemp::find($temp_id)->news_link;
            $news_link = $news_link ?:'';
            MessageService::notice(MessageTemp::$template_id, $msg, $refundApply->uid, $uniacid,$news_link);
        }


        //小程序消息
        $is_open = MinAppTemplateMessage::getTitle('退款拒绝通知');
        if (!$is_open->is_open){
            return;
        }
        $msg = [
            'keyword1'=>['value'=>  $memberDate['nickname']],// 商户名称
            'keyword2'=>['value'=> $refundApply->refund_sn],//订单编号
            'keyword3'=>['value'=> $refundApply->create_time],// 退款时间
            'keyword4'=>['value'=> $refundApply->price],// 退款金额
            'keyword5'=>['value'=> $refundApply->reason],// 退款理由
            'keyword6'=>['value'=> $refundApply->reject_reason],// 拒绝原因
        ];

        MessageService::MiniNotice($is_open->template_id,$msg,$refundApply->uid);
    }

    public static function passMessage($refundApply,$uniacid = '')
    {
        $couponNotice = Setting::get('shop.notice');
        $temp_id = $couponNotice['order_refund_success'];

        $memberDate = Member::getMemberBaseInfoById($refundApply->uid);
        $orderDate = Order::getOrderDetailById($refundApply->order_id);
//        $goods = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('goods_option_title');//商品详情
//        $goods_title = Order::find($refundApply->order_id)->hasManyOrderGoods()->value('title').$goods;
        if ($temp_id) {
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $memberDate['nickname']],
                ['name' => '退款单号', 'value' => $refundApply->refund_sn],
//            ['name' => '下单时间', 'value' => $orderDate['create_time']],
//            ['name' => '订单金额', 'value' => $orderDate['price']],
//            ['name' => '运费', 'value' => $orderDate['dispatch_price']],
//            ['name' => '商品详情（含规格）', 'value' => $goods_title],
//            ['name' => '支付方式', 'value' => $orderDate->pay_type_name],
//            ['name' => '支付时间', 'value' => $orderDate->pay_time],
                ['name' => '退款申请时间', 'value' => $refundApply->create_time],
                ['name' => '退款方式', 'value' => $orderDate->pay_type_name],
                ['name' => '退款金额', 'value' => $refundApply->price],
                ['name' => '退款原因', 'value' => $refundApply->reason],
                ['name' => '退款成功时间', 'value' => date('Y-m-d H:i:s', time())],
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return false;
            }
            $news_link = MessageTemp::find($temp_id)->news_link;
            $news_link = $news_link ?:'';
            MessageService::notice(MessageTemp::$template_id, $msg, $refundApply->uid, $uniacid,$news_link);
        }

        //小程序消息
        $is_open = MinAppTemplateMessage::getTitle('退款成功通知');
        if (!$is_open->is_open){
            return;
        }
        $orderDate = Order::getOrderDetailById($refundApply->order_id);
        $msg = [
            'keyword1'=>['value'=>  $memberDate['nickname']],// 退款人
            'keyword2'=>['value'=> $refundApply->refund_sn],//退款单号
            'keyword3'=>['value'=> date('Y-m-d H:i:s', time())],// 退款时间
            'keyword4'=>['value'=> $orderDate->pay_type_name],// 退款方式
            'keyword5'=>['value'=> $refundApply->price],// 退款金额
            'keyword6'=>['value'=> $refundApply->reason],// 退款原因
        ];

        MessageService::MiniNotice($is_open->template_id,$msg,$refundApply->uid);
    }

}