<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\models\Address;
use app\common\models\Order;
use app\common\models\Setting;
use app\common\modules\address\services\AddressService;
use app\common\repositories\MemberAddressRepository;
use app\frontend\models\Member;
use app\frontend\modules\payment\orderPayments\BasePayment;
use Yunshop\StoreCashier\common\models\Store;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public $transactionActions = [''];

    public function index()
    {
        $a = app('MemberAddressRepository');
        dd($a);
        exit;

        $paymentTypes = app('PaymentManager')->make('OrderPaymentTypeManager')->getOrderPaymentTypes();
        dd($paymentTypes);
        exit;

    }

}