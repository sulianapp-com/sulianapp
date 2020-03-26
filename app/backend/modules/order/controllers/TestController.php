<?php

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\Order;
use app\common\models\OrderAddress;
use app\common\services\TestContract;
use Illuminate\Support\Facades\Schema;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends BaseController
{
    public $transactionActions = ['*'];

    public function receiveEvent()
    {
        event(new AfterOrderReceivedEvent(Order::find(308)));
        dd(1);
    }
}