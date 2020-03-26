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
use app\common\exceptions\AppException;
use app\frontend\modules\payment\orderPayments\BasePayment;
use app\frontend\modules\payment\paymentSettings\PaymentSetting;
use Illuminate\Database\Eloquent\Builder;

class DetailController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $orderPay = OrderPay::with(['orders' => function ($query) {
            $query->with('orderGoods');
        }, 'process', 'member', 'payOrder']);
        if (request()->has('order_pay_id')) {
            $orderPay = $orderPay->find(request('order_pay_id'));
        }

        if (request()->has('pay_sn')) {
            $orderPay = $orderPay->where('pay_sn',request('pay_sn'))->first();
        }
        if(!$orderPay){
            throw new AppException('未找到支付记录');
        }
        return view('orderPay.detail', [
            'orderPay' => json_encode($orderPay)
        ])->render();
    }

    public function allCashierPayTypes()
    {
        new OrderPay(['amount', 100]);
    }

    public function usablePayTypes()
    {
        $orderPayId = request()->query('order_pay_id');
        $orderPay = OrderPay::with(['orders' => function ($query) {
            $query->with('orderGoods');
        }, 'process', 'member', 'payOrder'])->find($orderPayId);

        $orderPay->getPaymentTypes()->each(function (BasePayment $paymentType) {
            if (is_null($paymentType)) {
                return;
            }
            dump($paymentType->getName());
            $paymentType->getOrderPaymentSettings()->each(function (PaymentSetting $setting) {
                dump(get_class($setting));
                dump($setting->canUse());
                dump($setting->exist());
            });
        });
    }

    public function allPayTypes()
    {
        $orderPayId = request()->query('order_pay_id');
        $orderPay = OrderPay::with(['orders' => function ($query) {
            $query->with('orderGoods');
        }, 'process', 'member', 'payOrder'])->find($orderPayId);

        $orderPay->getAllPaymentTypes()->each(function (BasePayment $paymentType) {
            if (is_null($paymentType)) {
                return;
            }
            dump($paymentType->getName());
            $paymentType->getOrderPaymentSettings()->each(function (PaymentSetting $setting) {
                dump(get_class($setting));
                dump($setting->canUse());
                dump($setting->exist());
            });
        });
    }
}