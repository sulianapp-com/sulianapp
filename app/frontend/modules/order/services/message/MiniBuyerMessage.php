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
class MiniBuyerMessage extends Message
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
        \Log::debug('===============',[$uid,$this->formId]);
        $this->MiniNotice($this->templateId, $this->msg, $uid,'','',$this->formId);

    }

    private function transfer($temp_id, $params)
    {
        $this->msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$this->msg) {
            return;
        }
        $this->templateId = MessageTemp::$template_id;
        $this->sendToBuyer();
    }

    public function paymentSuccess($title){
        $is_open = MinAppTemplateMessage::getTitle($title);
        \Log::info("小程序通知测试-7",$is_open);
        if (!$is_open->is_open){
            return;
        }
        $this->msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],//姓名
            'keyword2'=>['value'=> $this->order->order_sn],//订单号码
            'keyword3'=>['value'=>  $this->order['create_time']->toDateTimeString()],//下单时间
            'keyword4'=>['value'=>  $this->order['price']],//订单金额
            'keyword5'=>['value'=> $this->goods_title],// 商品名称
            'keyword6'=>['value'=> $this->order->pay_type_name],//支付方式
            'keyword7'=>['value'=>  $this->order['pay_time']->toDateTimeString()],//支付时间
        ];
       $this->templateId = $is_open->template_id;
       $this->sendToBuyer();
    }

    //订单发货提醒
    public function delivery($title){
        $is_open = MinAppTemplateMessage::getTitle($title);
        if (!$is_open->is_open){
            return;
        }
        $this->msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],// 用户
            'keyword2'=>['value'=> $this->order->order_sn],//订单号
            'keyword3'=>['value'=>  $this->order['create_time']->toDateTimeString()],//下单时间
            'keyword4'=>['value'=> $this->order['price']],// 订单金额
            'keyword5'=>['value'=>  $this->goods_title],//商品信息
            'keyword6'=>['value'=>   $this->order['send_time']->toDateTimeString()],//发货时间
            'keyword7'=>['value'=>  $this->order['express']['express_company_name'] ?: "暂无信息"],//快递公司
            'keyword8'=>['value'=> $this->order['express']['express_sn'] ?: "暂无信息"],//快递单号
        ];
        $this->templateId = $is_open->template_id;
        $this->sendToBuyer();
    }

    public function canceled($title)
    {
        $is_open = MinAppTemplateMessage::getTitle($title);
        if (!$is_open->is_open){
            return;
        }
        $this->msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],// 用户
            'keyword2'=>['value'=> $this->order->order_sn],//订单号
            'keyword3'=>['value'=>  $this->order['create_time']->toDateTimeString()],//下单时间
            'keyword4'=>['value'=> $this->order['price']],// 订单金额
            'keyword5'=>['value'=> $this->order['dispatch_price']],//运费
            'keyword6'=>['value'=> $this->goods_title],//商品详情
            'keyword7'=>['value'=> $this->order['cancel_time']->toDateTimeString()],//取消时间
        ];
        $this->templateId = $is_open->template_id;
        $this->sendToBuyer();
    }
}