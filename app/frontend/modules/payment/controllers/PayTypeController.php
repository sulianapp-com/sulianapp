<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/16
 * Time: 下午3:13
 */

namespace app\frontend\modules\payment\controllers;


use app\common\components\BaseController;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\order\OrderCollection;
// use app\frontend\modules\order\services\behavior\OrderPay;
use app\common\models\OrderPay;
use app\frontend\modules\payment\orderPayments\BasePayment;

class PayTypeController extends BaseController
{
    public function index()
    {
        $buttons = [];
        $filter_minPayType = [1, 3, 28, 'cashPay'];
        $client_type = \YunShop::request()->type;
        $orderPay = new OrderPay(['amount' => request()->input('price', 0.01)]);
        // todo 可以将添加订单的方法添加到收银台model中
        $order = new PreOrder(['is_virtual'=>1]);
        $orderPay->setRelation('orders',new OrderCollection([$order]));
        $paymentTypes = app('PaymentManager')->make('OrderPaymentTypeManager')->getOrderPaymentTypes($orderPay);

         $paymentTypes->map(function (BasePayment $paymentType) {
            return [
                'name' => $paymentType->getName(),
                'value' => $paymentType->getId(),
                'need_password' => $paymentType->needPassword(),
            ];
        })->each(function($item, $key) use (&$buttons, $filter_minPayType, $client_type) {
             if ($item['value'] != 14 && $item['value'] != 18) {
                 switch ($client_type) {
                     case 1:
                         $buttons[] = $item;
                         break;
                     case 2:
                         if (in_array($item['value'], $filter_minPayType)) {
                             $buttons[] = $item;
                         }
                         break;
                     default:
                         $buttons[] = $item;
                 }
             }
        });

        $data = ['buttons' => $buttons];

        return $this->successJson('成功', $data);
    }
}