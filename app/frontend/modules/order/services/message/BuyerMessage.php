<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\Member;
use app\common\models\MemberShopInfo;
use app\common\models\notice\MessageTemp;
use app\common\models\notice\MinAppTemplateMessage;
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:15
 */
class BuyerMessage extends Message
{
    protected $goods_title;

    public function __construct($order,$formId = '',$type = 1,$title)
    {
        parent::__construct($order,$formId,$type,$title);
        $this->goods_title = $this->order->hasManyOrderGoods()->first()->title;
        $this->goods_title .= $this->order->hasManyOrderGoods()->first()->goods_option_title ?: '';
    }

    protected function sendToBuyer()
    {
        try {
            return $this->sendToMember($this->order->uid);
        } catch (\Exception $exception) {

        }
    }

    protected function sendToMember($uid)
    {
        if (empty($this->templateId)) {
            return;
        }
        $this->notice($this->templateId, $this->msg, $uid,'',$this->news_link);
    }

    protected function miniSendToShops($templateId,$msg)
    {
        if (empty($templateId)) {
            return;
        }
        \Log::debug('===============',[$templateId]);
        $this->MiniNotice($templateId, $msg, $this->order->uid);
    }

    private function transfer($temp_id, $params)
    {
        $this->msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$this->msg) {
            return;
        }
        $this->templateId = MessageTemp::$template_id;
        $news_link = MessageTemp::find($temp_id)->news_link;
        $this->news_link = $news_link ?:'';
        $this->sendToBuyer();
    }

    public function created()
    {
        $temp_id = \Setting::get('shop.notice')['order_submit_success'];
        if ($temp_id) {
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
                ['name' => '订单号', 'value' => $this->order->order_sn],
                ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
                ['name' => '订单金额', 'value' => $this->order['price']],
                ['name' => '运费', 'value' => $this->order['dispatch_price']],
                ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ];
            $this->transfer($temp_id, $params);
        }


        //小程序消息
        $is_open = MinAppTemplateMessage::getTitle('订单提交成功通知');
        if (!$is_open->is_open){
            return;
        }
        $msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],// 客户姓名
            'keyword2'=>['value'=> $this->order->order_sn],//订单号
            'keyword3'=>['value'=> $this->order['create_time']->toDateTimeString()],// 下单时间
            'keyword4'=>['value'=> $this->order['price']],//  订单金额
            'keyword5'=>['value'=> $this->goods_title],//  商品信息
        ];
        $this->miniSendToShops($is_open->template_id,$msg);
    }

    public function paid()
    {
        $temp_id = \Setting::get('shop.notice')['order_pay_success'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
            ['name' => '订单号', 'value' => $this->order->order_sn],
            ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
            ['name' => '订单金额', 'value' => $this->order['price']],
            ['name' => '运费', 'value' => $this->order['dispatch_price']],
            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
            ['name' => '支付方式', 'value' => $this->order->pay_type_name],
            ['name' => '支付时间', 'value' => $this->order['pay_time']->toDateTimeString()],
        ];
        $this->transfer($temp_id, $params);


    }

//    public function canceled()
//    {
//        $temp_id = \Setting::get('shop.notice')['order_cancel'];
//        if (!$temp_id) {
//            return;
//        }
//        $params = [
//            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
//            ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
//            ['name' => '订单号', 'value' => $this->order->order_sn],
//            ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
//            ['name' => '订单金额', 'value' => $this->order['price']],
//            ['name' => '运费', 'value' => $this->order['dispatch_price']],
//            ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
//            ['name' => '支付方式', 'value' => $this->order->pay_type_name],
//            ['name' => '订单取消时间', 'value' => $this->order['cancel_time']->toDateTimeString()],
//        ];
//        $this->transfer($temp_id, $params);
//    }
    public function canceled()
    {
        $temp_id = \Setting::get('shop.notice')['order_cancel'];
        if ($temp_id) {
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
                ['name' => '订单号', 'value' => $this->order->order_sn],
                ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
                ['name' => '订单金额', 'value' => $this->order['price']],
                ['name' => '运费', 'value' => $this->order['dispatch_price']],
                ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
                ['name' => '支付方式', 'value' => $this->order->pay_type_name],
                ['name' => '订单取消时间', 'value' => $this->order['cancel_time']->toDateTimeString()],
            ];
            $this->transfer($temp_id, $params);
        }

        //小程序消息
        $is_open = MinAppTemplateMessage::getTitle('订单取消通知');
        if (!$is_open->is_open){
            return;
        }
        $msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],//  用户名
            'keyword2'=>['value'=> $this->order->order_sn],//订单号
            'keyword3'=>['value'=> $this->order['create_time']->toDateTimeString()],// 下单时间
            'keyword4'=>['value'=> $this->order['price']],//  订单金额
            'keyword5'=>['value'=> $this->order['price']],//  订单运费
            'keyword6'=>['value'=> $this->goods_title],//  商品详情
            'keyword7'=>['value'=> $this->order['cancel_time']->toDateTimeString()],//  取消时间
        ];
        $this->miniSendToShops($is_open->template_id,$msg);
    }

    public function sent()
    {
        $temp_id = \Setting::get('shop.notice')['order_send'];
        if ($temp_id) {
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
                ['name' => '订单号', 'value' => $this->order->order_sn],
                ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
                ['name' => '订单金额', 'value' => $this->order['price']],
                ['name' => '运费', 'value' => $this->order['dispatch_price']],
                ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
                ['name' => '发货时间', 'value' => $this->order['send_time']->toDateTimeString()],
                ['name' => '快递公司', 'value' => $this->order['express']['express_company_name'] ?: "暂无信息"],
                ['name' => '快递单号', 'value' => $this->order['express']['express_sn'] ?: "暂无信息"],
            ];
            $this->transfer($temp_id, $params);
        }


        //小程序消息模板
        $is_open = MinAppTemplateMessage::getTitle('订单发货提醒');
        if (!$is_open->is_open){
            return;
        }
        $msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],//  用户名
            'keyword2'=>['value'=> $this->order->order_sn],//订单号
            'keyword3'=>['value'=> $this->order['create_time']->toDateTimeString()],// 下单时间
            'keyword4'=>['value'=> $this->order['price']],//  订单金额
            'keyword5'=>['value'=> $this->goods_title],//  商品信息
            'keyword6'=>['value'=> $this->order['send_time']->toDateTimeString()],//  发货时间
            'keyword7'=>['value'=> $this->order['express']['express_company_name'] ?: "暂无信息"],//  快递公司
            'keyword8'=>['value'=> $this->order['express']['express_sn'] ?: "暂无信息"],//  快递单号
        ];
        $this->miniSendToShops($is_open->template_id,$msg);
    }

    public function received()
    {
        $temp_id = \Setting::get('shop.notice')['order_finish'];
        if ($temp_id) {
            $params = [
                ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
                ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
                ['name' => '订单号', 'value' => $this->order->order_sn],
                ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
                ['name' => '订单金额', 'value' => $this->order['price']],
                ['name' => '运费', 'value' => $this->order['dispatch_price']],
                ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
                ['name' => '确认收货时间', 'value' => $this->order['finish_time']->toDateTimeString()],
            ];
            $this->transfer($temp_id, $params);
        }


        //小程序消息模板
        $is_open = MinAppTemplateMessage::getTitle('确认收货通知');
        if (!$is_open->is_open){
            return;
        }
        $msg = [
            'keyword1'=>['value'=> $this->goods_title],//  商品名称
            'keyword2'=>['value'=> $this->order->belongsToMember->nickname],//买家昵称
            'keyword3'=>['value'=>  $this->order->order_sn],// 订单编号
            'keyword4'=>['value'=> $this->order['create_time']->toDateTimeString()],//  订订单时间
            'keyword5'=>['value'=> $this->order['price']],//  订单金额
            'keyword6'=>['value'=> $this->order['finish_time']->toDateTimeString()],//  确认收货时间
        ];
        $this->miniSendToShops($is_open->template_id,$msg);
    }
}