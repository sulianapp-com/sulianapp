<?php

namespace app\frontend\modules\order\services\message;

use app\common\models\Order;
use app\common\services\MessageService;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/7
 * Time: 上午10:18
 */
class Message extends MessageService
{

    /**
     * @var Order
     */
    protected $order;
    protected $msg;
    protected $templateId;
    protected $notice;
    protected $noticeType;
    protected $formId;
    protected $title;
    protected $news_link;
    function __construct($order,$formId = '',$type = 1,$title)
    {
        $this->order = $order;
        $this->formId = $formId;
        $this->noticeType = $type;
        $this->title = $title;
    }

}