<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\Notice;
use app\common\models\notice\MessageTemp;
use app\common\models\notice\MinAppTemplateMessage;
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:15
 */
class ShopMessage extends Message
{
    protected $goods_title;
    public function __construct($order,$formId = '',$type = 1,$title)
    {
        parent::__construct($order,$formId = '',$type = 1,$title);
        $this->goods_title = $this->order->hasManyOrderGoods()->first()->title;
        $this->goods_title .= $this->order->hasManyOrderGoods()->first()->goods_option_title ? '['.$this->order->hasManyOrderGoods()->first()->goods_option_title.']': '';
    }

    private function sendToShops()
    {
        if (empty(\Setting::get('shop.notice.salers'))) {
            return;
        }
        if (empty($this->templateId)) {
            return;
        }
        //客服发送消息通知

        foreach (\Setting::get('shop.notice.salers') as $saler) {
                $this->notice($this->templateId, $this->msg, $saler['uid'],'',$this->news_link);
        }
    }
    protected function miniSendToShops($templateId,$msg)
    {
        if (empty($templateId)) {
            return;
        }
        \Log::debug('===============',[$templateId]);
        foreach (\Setting::get('mini_app.notice.salers') as $saler) {
            $this->MiniNotice($templateId, $msg, $saler['uid']);
        }
    }

    private function transfer($temp_id, $params)
    {
        $this->msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$this->msg) {
            return;
        }

        $news_link = MessageTemp::find($temp_id)->news_link;
        $this->news_link = $news_link ?:'';

        $this->templateId = MessageTemp::$template_id;
        $this->sendToShops();
    }

    public function created()
    {
        $temp_id = \Setting::get('shop.notice')['seller_order_create'];
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


        if (!\Setting::get('mini_app.notice.notice_enable.created')) {
            return ;
        }

        //小程序消息通知
        $is_open = MinAppTemplateMessage::getTitle('订单生成通知');

        if (!$is_open->is_open){
            return;
        }
        \Log::info("小程序通知测试-4",$is_open);
        $address = $this->order['address'];
        $msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],// 订单发起者
            'keyword2'=>['value'=> $this->goods_title],//商品信息
            'keyword3'=>['value'=>  $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address']],// 收货地址
            'keyword4'=>['value'=> $this->order['price']],// 订单金额
            'keyword5'=>['value'=> $this->order['create_time']->toDateTimeString()],// 生成时间
            'keyword6'=>['value'=>$this->order->order_sn],//订单号
        ];
        $this->miniSendToShops($is_open->template_id, $msg);
    }

    public function paid()
    {
        $temp_id = \Setting::get('shop.notice')['seller_order_pay'];
        if ($temp_id) {
            $address = $this->order['address'];
            $params = [
                ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
                ['name' => '订单号', 'value' => $this->order->order_sn],
                ['name' => '下单时间', 'value' => $this->order['create_time']->toDateTimeString()],
                ['name' => '支付时间', 'value' => $this->order['pay_time']->toDateTimeString()],
                ['name' => '支付方式', 'value' => $this->order->pay_type_name],
                ['name' => '订单金额', 'value' => $this->order['price']],
                ['name' => '运费', 'value' => $this->order['dispatch_price']],
                ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
                ['name' => '收件人姓名', 'value' => $address['realname']],
                ['name' => '收件人电话', 'value' => $address['mobile']],
                ['name' => '收件人地址', 'value' => $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address']],
            ];
            $this->transfer($temp_id, $params);
        }

        if (!\Setting::get('mini_app.notice.notice_enable.paid')) {
            return ;
        }

        //小程序消息通知
        $is_open = MinAppTemplateMessage::getTitle('订单支付提醒');

        if (!$is_open->is_open){
            return;
        }
        \Log::info("小程序通知测试-5",$is_open);
        $address = $this->order['address'];
        $msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],// 用户
            'keyword2'=>['value'=> $this->order->order_sn],//订单号
            'keyword3'=>['value'=>  $this->goods_title],//商品名称
            'keyword4'=>['value'=> $this->order->pay_type_name],// 支付方式
            'keyword5'=>['value'=> $this->order['price']],// 支付金额
            'keyword6'=>['value'=> $address['realname']],//收貨人
            'keyword7'=>['value'=>   $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address']],//收貨地址
        ];
        $this->miniSendToShops($is_open->template_id, $msg);
    }

    public function received()
    {
        $temp_id = \Setting::get('shop.notice')['seller_order_finish'];
        if ($temp_id) {
            $address = $this->order['address'];
            $params = [
                ['name' => '粉丝昵称', 'value' => $this->order->belongsToMember->nickname],
                ['name' => '订单号', 'value' => $this->order->order_sn],
                ['name' => '确认收货时间', 'value' => $this->order['finish_time']->toDateTimeString()],
                ['name' => '运费', 'value' => $this->order['dispatch_price']],
                ['name' => '商品详情（含规格）', 'value' => $this->goods_title],
                ['name' => '收件人姓名', 'value' => $address['realname']],
                ['name' => '收件人电话', 'value' => $address['mobile']],
                ['name' => '收件人地址', 'value' => $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address']],
            ];
            $this->transfer($temp_id, $params);
        }

        if (!\Setting::get('mini_app.notice.notice_enable.received')) {
            return ;
        }

        //小程序消息
        $is_open = MinAppTemplateMessage::getTitle('确认收货通知');

        if (!$is_open->is_open){
            return;
        }
        \Log::info("小程序通知测试-6",$is_open);
        $msg = [
            'keyword1'=>['value'=> $this->goods_title],// 商品名
            'keyword2'=>['value'=> $this->order->belongsToMember->nickname],//买家昵称
            'keyword3'=>['value'=> $this->order->order_sn],//  订单编号
            'keyword4'=>['value'=> $this->order['create_time']->toDateTimeString()],//  订单时间
            'keyword5'=>['value'=> $this->order['price']],//订单金額
            'keyword6'=>['value'=> $this->order['finish_time']->toDateTimeString()],//  确认收货时间
        ];
        $this->miniSendToShops($is_open->template_id,$msg);

    }

    /**
     * @name 购买商品发送通知
     * @author
     * @param $status
     */
    public function goodsBuy($status)
    {
        $order_goods = $this->order->hasManyOrderGoods()->get();
        foreach ($order_goods as $goods) {
            $goods_notice = Notice::select()->where('goods_id', $goods->goods_id)->whereType($status)->first();
            if (!$goods_notice) {
                continue;
            }
            $temp_id = \Setting::get('shop.notice')['buy_goods_msg'];
            if ($temp_id) {
                $params = [
                    ['name' => '会员昵称', 'value' => $this->order->belongsToMember->nickname],
                    ['name' => '订单编号', 'value' => $this->order->order_sn],
                    ['name' => '商品名称（含规格）', 'value' => $this->getGoodsTitle($goods)],
                    ['name' => '商品金额', 'value' => $goods->price],
                    ['name' => '商品数量', 'value' => $goods->total],
                    ['name' => '订单状态', 'value' => $this->order->status_name],
                    ['name' => '时间', 'value' => $this->getOrderTime($status)],
                ];
                $msg = MessageTemp::getSendMsg($temp_id, $params);
                if (!$msg) {
                    continue;
                }
                $news_link = MessageTemp::find($temp_id)->news_link;
                $news_link = $news_link ?:'';
                $template_id = MessageTemp::$template_id;
                $this->notice($template_id, $msg, $goods_notice->uid,'',$news_link);
            }

            //小程序消息通知
            $is_open = MinAppTemplateMessage::getTitle('购买成功通知');
            if (!$is_open->is_open){
                return;
            }
            $miniParams = [
                'keyword1'=>['value'=> $this->order->belongsToMember->nickname],// 会员姓名
                'keyword2'=>['value'=>  $this->order->order_sn],//订单号
                'keyword3'=>['value'=> $this->getGoodsTitle($goods)],// 物品名称
                'keyword4'=>['value'=> $goods->total],//  数量
                'keyword5'=>['value'=> $goods->price],// 购买金额
                'keyword6'=>['value'=> $this->getOrderTime($status)],//购买时间
            ];
            $this->miniSendToShops($is_open->template_id,$miniParams);
        }
    }

    /**
     * @name 获取订单操作时间
     * @author
     * @param $status
     * @return mixed
     */
    private function getOrderTime($status)
    {
        if ($status == 1) {
            $order_time = $this->order['create_time']->toDateTimeString();
        } else if ($status == 2) {
            $order_time = $this->order['pay_time']->toDateTimeString();
        } else if ($status == 3) {
            $order_time = $this->order['finish_time']->toDateTimeString();
        }
        return $order_time;
    }

    /**
     * @name 获取商品名
     * @author
     * @param $goods
     * @return string
     */
    private function getGoodsTitle($goods)
    {
        $goods_title = $goods->title;
        if ($goods->goods_option_title) {
            $goods_title .= '[' . $goods->goods_option_title . ']';
        }
        return $goods_title;
    }
}