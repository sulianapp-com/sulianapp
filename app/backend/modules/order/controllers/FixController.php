<?php

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\fix\OrderPayFailRepair;
use app\backend\modules\order\models\OrderOperationLog;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\models\Address;
use app\common\models\Member;
use app\common\models\MemberAddress;
use app\common\models\Order;
use app\common\models\order\FirstOrder;
use app\common\models\OrderAddress;
use app\common\models\OrderGoods;
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use app\common\facades\Setting;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class FixController extends BaseController
{
    public $transactionActions = 'payFail';

    public function yy()
    {
        $order = Order::find(3401);
        \YunShop::app()->uniacid = $order->uniacid;
        Setting::$uniqueAccountId = $order->uniacid;

        $shopOrderSet = Setting::get('shop.order');
        if (!$shopOrderSet['goods']) {
            return;
        }

        if ($order->is_plugin != 0 || $order->plugin_id != 0) {
            return;
        }

        foreach ($order->hasManyOrderGoods as $orderGoods) {
            if ($shopOrderSet['goods'][$orderGoods->goods_id]) {

                $firstOrder = FirstOrder::select()
                    ->where('uid', $order->uid)
                    ->where('goods_id', $orderGoods->goods_id)
                    ->first();
                if ($firstOrder) {
                    continue;
                }
                dump([
                    'order_id' => $order->id,
                    'goods_id' => $orderGoods->goods_id,
                    'uid' => $order->uid,
                    'shop_order_set' => $shopOrderSet['goods']
                ]);
            }
        }
        dd('ok');
        exit;
    }

    public function fixOrderAddress()
    {
        $orders = Order::where(
            [
                'plugin_id' => 0,
                'is_virtual' => 0,
            ]
        )->where('id', [534])->get();
        $orders->each(function ($order) {

            $memberAddress = $order->belongsToMember->defaultAddress;
            $result['address'] = implode(' ', [$memberAddress->province, $memberAddress->city, $memberAddress->district, $memberAddress->address]);
            $result['mobile'] = $memberAddress->mobile;
            $result['address'] = implode(' ', [$memberAddress->province, $memberAddress->city, $memberAddress->district, $memberAddress->address]);
            $result['realname'] = $memberAddress->username;
            $result['order_id'] = $order->id;

            list($result['province_id'], $result['city_id'], $result['district_id']) = Address::whereIn('areaname', [$memberAddress->province, $memberAddress->city, $memberAddress->district])->pluck('id');

            $orderAddress = new OrderAddress($result);
            $orderAddress->save();
            $order->dispatch_type_id = 1;
            $order->save();
        });

    }

    public function fixOrderPayId()
    {

        $r = Order::where('pay_time', '>', 0)->where(function ($query) {
            return $query->wherePayTypeId(0)->orWhere('order_pay_id', 0);
        })->get();
        $r->each(function ($order) {

            $orderPay = OrderPay::where(['order_ids' => '["' . $order->id . '"]'])->orderBy('id', 'desc')->first();

            if (isset($orderPay)) {
                $order->pay_type_id = $orderPay->pay_type_id;
                $order->order_pay_id = $orderPay->id;
                $order->save();
            }

        });
        echo 1;
        exit;

    }

    public function time()
    {
        Order::whereIn('status', [0, 1, 2, 3])->where('create_time', 0)->update(['create_time' => time()]);
        Order::whereIn('status', [1, 2, 3])->where('pay_time', 0)->update(['pay_time' => time()]);
        Order::whereIn('status', [2, 3])->where('send_time', 0)->update(['send_time' => time()]);
        Order::whereIn('status', [3])->where('finish_time', 0)->update(['finish_time' => time()]);
        Order::where('status', '-1')->where('cancel_time', 0)->update(['cancel_time' => time()]);
        echo 'ok';

    }

    public function deleteInvalidOrders()
    {
        Order::doesntHave('hasManyOrderGoods')->delete();
        Order::where('goods_price', '<=', 0)->delete();
        OrderGoods::where('goods_price', '<=', 0)->delete();
        echo 'ok';

    }

    public function payType()
    {
        Order::whereIn('status', [1, 2, 3])->where('pay_type_id', 0)->update(['pay_type_id' => 1]);
        echo 'ok';

    }

    public function dispatchType()
    {
        Order::whereIn('status', [2, 3])->where('dispatch_type_id', 0)->update(['dispatch_type_id' => 1]);
        echo 'ok';

    }

    public function index()
    {
        $payOrders = PayOrder::where('updated_at', '>', 0)->get();

        $payOrders->each(function ($payOrder) {
            $orderPay = OrderPay::wherePaySn($payOrder->out_order_no)->first();
            $orders = Order::whereIn('id', $orderPay->order_ids)->get();

            $orders->each(function ($order) use ($payOrder) {
                if ($order->pay_type_id == 0 && $order->status > 0) {
                    if ($payOrder->third_type == '余额') {
                        $order->pay_type_id = 3;
                    } elseif ($payOrder->third_type == '支付宝') {
                        $order->pay_type_id = 2;
                    } elseif ($payOrder->third_type == '微信') {
                        $order->pay_type_id = 1;
                    }
                    $order->save();
                }
            });
        });

    }

    public function t()
    {
        $a = PayOrder::where('trade_no','4200000437201910259512165417')->first();
        $b = OrderPay::where('pay_sn','PN20191025210634uf')->first();
        $c = OrderOperationLog::where('order_id',10044)->get();
        dd($a->toArray(),$b->toArray(),$b->orders->toArray(),$c->toArray());
    }

    /**
     * @throws \app\common\exceptions\AppException
     */
    public function payFail()
    {
        $order = Order::find(request('order_id'));
        $order->status=0;
        $order->save();
        if (!$order) {
            throw new AppException('未找到订单');
        }
        $a = new OrderPayFailRepair($order);
        $a->handle();
        dd($a->message);
    }
}