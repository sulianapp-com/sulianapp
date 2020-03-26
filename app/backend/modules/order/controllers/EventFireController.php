<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/3/8
 * Time: 11:06 AM
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\events\order\AfterOrderCreatedEvent;
use Yunshop\TripartiteProvider\models\Order;

class EventFireController extends BaseController
{
    public $transactionActions = ['*'];
    public function created()
    {
        dd(Order::find('14150'));
        event(new AfterOrderCreatedEvent(Order::find('14150')));
        dd(1);
    }

    public function paid()
    {

    }
}