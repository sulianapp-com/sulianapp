<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/5
 * Time: 下午7:53
 */

namespace app\frontend\modules\order\services;


use app\frontend\modules\order\services\message\MiniBuyerMessage;
use app\frontend\modules\order\services\message\MiniShopMessage;

class MiniMessageService extends \app\common\services\MessageService
{
    private $buyerMessage;
    private $shopMessage;
    protected $formId;
    protected $noticeType;
    function __construct($order,$formId = '',$type = 1,$title = '')
    {
        $this->buyerMessage = new MiniBuyerMessage($order,$formId,$type,$title);
        $this->shopMessage = new MiniShopMessage($order,$formId,$type,$title);
        $this->formId = $formId;
        $this->noticeType = $type;
    }

//    public function canceled()
//    {
//        $this->buyerMessage->canceled();
//
//    }
//
//    public function created()
//    {
//        $this->shopMessage->goodsBuy(1);
//        $this->buyerMessage->created();
//        if (\Setting::get('shop.notice.notice_enable.created')) {
//            $this->shopMessage->created();
//        }
//    }
//
//    public function paid()
//    {
//        $this->shopMessage->goodsBuy(2);
//        $this->buyerMessage->paid();
//
//        if (\Setting::get('shop.notice.notice_enable.paid')) {
//            $this->shopMessage->paid();
//        }
//
//    }
//
//    public function sent()
//    {
//        $this->buyerMessage->sent();
//
//    }
    public function refund()
    {
        $this->buyerMessage->delivery('订单发货提醒');
    }

    public function canceled()
    {
        $this->buyerMessage->canceled('订单取消通知');
    }

    public function received()
    {
        if ($this->noticeType == 2) {
            $this->shopMessage->paymentRemind('订单支付成功提醒');
        }
        $this->buyerMessage->paymentSuccess('订单支付成功通知');
    }
}