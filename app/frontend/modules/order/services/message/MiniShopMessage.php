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
class MiniShopMessage extends Message
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

        if (empty($this->templateId)) {
            return;
        }
        //客服发送消息通知
        $this->notice($this->templateId, $this->msg, $this->order->uid);
    }

    public function miniSendToShops($templateId,$msg){
        if (empty($templateId)) {
            return;
        }
        //客服发送消息通知
        $this->MiniNotice($templateId, $msg, $this->order->uid);
    }

    public function paymentRemind($title){
        $is_open = MinAppTemplateMessage::getTitle($title);
        \Log::info("小程序通知测试-8",$is_open);
        if (!$is_open->is_open){
            return;
        }
        $address = $this->order['address'];
        $this->msg = [
            'keyword1'=>['value'=> $this->order->belongsToMember->nickname],// 用户
            'keyword2'=>['value'=> $this->order->order_sn],//订单号
            'keyword3'=>['value'=>  $this->goods_title],//商品名称
            'keyword4'=>['value'=> $this->order->pay_type_name],// 支付方式
            'keyword5'=>['value'=> $this->order['price']],// 支付金额
            'keyword6'=>['value'=> $address['realname']],//收貨人
            'keyword7'=>['value'=>   $address['province'] . ' ' . $address['city'] . ' ' . $address['area'] . ' ' . $address['address']],//收貨地址
        ];
        $this->templateId = $is_open->template_id;
        $this->sendToShops();
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