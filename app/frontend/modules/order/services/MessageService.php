<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/5
 * Time: 下午7:53
 */

namespace app\frontend\modules\order\services;


use app\frontend\modules\order\services\message\BuyerMessage;
use app\frontend\modules\order\services\message\ShopMessage;

class MessageService extends \app\common\services\MessageService
{
    private $buyerMessage;
    private $shopMessage;
    protected $formId;
    protected $noticeType;
    function __construct($order,$formId = '',$type = 1,$title='')
    {
        $this->buyerMessage = new BuyerMessage($order,$formId,$type,$title);
        $this->shopMessage = new ShopMessage($order,$formId,$type,$title);
        $this->formId = $formId;
        $this->noticeType = $type;
    }

    public function canceled()
    {
        $this->buyerMessage->canceled();
    }

    public function created()
    {
        $this->shopMessage->goodsBuy(1);
        $this->buyerMessage->created();
        $this->shopMessage->created();
    }

    public function paid()
    {
        $this->shopMessage->goodsBuy(2);
        $this->buyerMessage->paid();
        $this->shopMessage->paid();
    }

    public function sent()
    {
        $this->buyerMessage->sent();
    }

    public function received()
    {
        $this->shopMessage->goodsBuy(3);
        $this->shopMessage->received();
        $this->buyerMessage->received();
    }
}