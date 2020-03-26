<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/11
 * Time: 下午2:31
 */

namespace app\backend\modules\orderPay\controllers;

use app\backend\modules\order\models\OrderPay;
use app\common\components\BaseController;
use app\frontend\modules\payment\orderPayments\BasePayment;
use app\frontend\modules\payment\paymentSettings\PaymentSetting;
use Illuminate\Database\Eloquent\Builder;

class IndexController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
       $orderPays = OrderPay::limit(20)->orderBy('id','desc')->get();
        dump($orderPays);
        exit;

    }

}