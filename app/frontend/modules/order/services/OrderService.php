<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/24
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\services;

use app\common\exceptions\AppException;
use app\common\models\Order;

use app\common\models\order\OrderGoodsChangePriceLog;
use app\common\modules\orderGoods\OrderGoodsCollection;
use \app\common\models\MemberCart;
use app\frontend\modules\order\services\behavior\OrderCancelPay;
use app\frontend\modules\order\services\behavior\OrderCancelSend;
use app\frontend\modules\order\services\behavior\OrderChangePrice;
use app\frontend\modules\order\services\behavior\OrderClose;
use app\frontend\modules\order\services\behavior\OrderDelete;
use app\frontend\modules\order\services\behavior\OrderForceClose;
use app\frontend\modules\order\services\behavior\OrderOperation;
use app\frontend\modules\order\services\behavior\OrderPay;
use app\frontend\modules\order\services\behavior\OrderReceive;
use app\frontend\modules\order\services\behavior\OrderSend;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * 获取订单商品对象数组
     * @param Collection $memberCarts
     * @return OrderGoodsCollection
     * @throws \Exception
     */
    public static function getOrderGoods(Collection $memberCarts)
    {
        if ($memberCarts->isEmpty()) {
            throw new AppException("购物车记录为空");
        }
        $result = $memberCarts->map(function ($memberCart) {
            if (!($memberCart instanceof MemberCart)) {
                throw new \Exception("请传入" . MemberCart::class . "的实例");
            }
            /**
             * @var $memberCart MemberCart
             */

            $data = [
                'goods_id' => (int)$memberCart->goods_id,
                'goods_option_id' => (int)$memberCart->option_id,
                'total' => (int)$memberCart->total,
            ];
            $orderGoods = app('OrderManager')->make('PreOrderGoods', $data);
            /**
             * @var PreOrderGoods $orderGoods
             */
            $orderGoods->setRelation('goods', $memberCart->goods);
            $orderGoods->setRelation('goodsOption', $memberCart->goodsOption);
            return $orderGoods;
        });

        return new PreOrderGoodsCollection($result);
    }

    /**
     * 获取订单号
     * @return string
     */
    public static function createOrderSN()
    {
        $orderSN = createNo('SN', true);
        while (1) {
            if (!Order::where('order_sn', $orderSN)->first()) {
                break;
            }
            $orderSN = createNo('SN', true);
        }
        return $orderSN;
    }

    /**
     * 获取支付流水号
     * @return string
     */
    public static function createPaySN()
    {
        $paySN = createNo('PN', true);
        while (1) {
            if (!\app\common\models\OrderPay::where('pay_sn', $paySN)->first()) {
                break;
            }
            $paySN = createNo('PN', true);
        }
        return $paySN;
    }

    /**
     * 订单操作类
     * @param OrderOperation $orderOperation
     * @return string
     * @throws AppException
     */
    private static function OrderOperate(OrderOperation $orderOperation)
    {
        if (!isset($orderOperation)) {
            throw new AppException('未找到该订单');
        }
        DB::transaction(function () use ($orderOperation) {
            $orderOperation->handle();
        });
    }

    /**
     * 取消付款
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderCancelPay($param)
    {
        $orderOperation = OrderCancelPay::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 取消发货
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderCancelSend($param)
    {
        $orderOperation = OrderCancelSend::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 关闭订单
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderClose($param)
    {
        $orderOperation = OrderClose::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 强制关闭订单
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderForceClose($param)
    {
        $orderOperation = OrderForceClose::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 用户删除(隐藏)订单
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderDelete($param)
    {
        $orderOperation = OrderDelete::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 根据流水号合并支付
     * @param array $param
     * @throws AppException
     */
    public static function ordersPay(array $param)
    {
        \Log::info('---------订单支付ordersPay(order_pay_id:' . $param['order_pay_id'] . ')--------', $param);
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find($param['order_pay_id']);
        if (!isset($orderPay)) {
            throw new AppException('支付流水记录不存在');
        }

        if (isset($param['pay_type_id'])) {
            if ($orderPay->pay_type_id != $param['pay_type_id']) {
                \Log::error("---------支付回调与与支付请求的订单支付方式不匹配(order_pay_id:{$orderPay->id},orderPay->payTypeId:{$orderPay->pay_type_id} != param[pay_type_id]:{$param['pay_type_id']})--------", []);
                $orderPay->pay_type_id = $param['pay_type_id'];

            }
        }
        $orderPay->pay();

        \Log::info('---------订单支付成功ordersPay(order_pay_id:' . $orderPay->id . ')--------', []);

    }

    /**
     * 后台支付订单
     * @param array $param
     * @return string
     * @throws AppException
     */

    public static function orderPay(array $param)
    {
        /**
         * @var OrderOperation $orderOperation
         */
        $orderOperation = OrderPay::find($param['order_id']);

        if (isset($param['pay_type_id'])) {
            $orderOperation->pay_type_id = $param['pay_type_id'];
        }
        $orderOperation->order_pay_id = (int)$param['order_pay_id'];

        $result = self::OrderOperate($orderOperation);
        //是虚拟商品或有标识直接完成
        if ($orderOperation->isVirtual() || $orderOperation->mark) {
            // 虚拟物品付款后直接完成
            $orderOperation->dispatch_type_id = 0;
            $orderOperation->save();
            self::orderSend(['order_id' => $orderOperation->id]);
            $result = self::orderReceive(['order_id' => $orderOperation->id]);
        } elseif (isset($orderOperation->hasOneDispatchType) && !$orderOperation->hasOneDispatchType->needSend()) {
            // 不需要发货的物品直接改为待收货
            self::orderSend(['order_id' => $orderOperation->id]);
        }

        return $result;
    }

    /**
     * 收货
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderReceive($param)
    {
        $orderOperation = OrderReceive::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 发货
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderSend($param)
    {
        // \Log::info('---param---', $param);
        $orderOperation = OrderSend::find($param['order_id']);

        $orderOperation->params = $param;
        // \Log::info('----1orderOperation--', $orderOperation);
        return self::OrderOperate($orderOperation);
    }

    /**
     * 改变订单价格
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function changeOrderPrice($param)
    {
        $order = OrderChangePrice::find($param['order_id']);
        /**
         * @var $order OrderChangePrice
         */
        if (!isset($order)) {
            throw new AppException('(ID:' . $order->id . ')未找到订单');
        }
        $orderGoodsChangePriceLogs = self::getOrderGoodsChangePriceLogs($param);

        $order->setOrderGoodsChangePriceLogs($orderGoodsChangePriceLogs);//todo
        $order->setOrderChangePriceLog();
        $order->setDispatchChangePrice($param['dispatch_price']);

        return self::OrderOperate($order);
    }

    /**
     * 订单改价记录
     * {@inheritdoc}
     */
    private static function getOrderGoodsChangePriceLogs($param)
    {
        return collect($param['order_goods'])->map(function ($orderGoodsParams) use ($param) {

            $orderGoodsChangePriceLog = new OrderGoodsChangePriceLog($orderGoodsParams);
            if (!isset($orderGoodsChangePriceLog->belongsToOrderGoods)) {
                throw new AppException('(ID:' . $orderGoodsChangePriceLog->order_goods_id . ')未找到订单商品记录');

            }
            if ($orderGoodsChangePriceLog->belongsToOrderGoods->order_id != $param['order_id']) {
                throw new AppException('(ID:' . $orderGoodsChangePriceLog->order_goods_id . ',' . $param['order_id'] . ')未找到与商品对应的订单');
            }
            //todo 如果不清空,可能会在push时 保存未被更新的订单商品数据,此处需要重新设计
            $orderGoodsChangePriceLog->setRelations([]);
            return $orderGoodsChangePriceLog;
        });
    }

    /**
     * 自动收货
     * {@inheritdoc}
     */
    public static function autoReceive($uniacid)
    {
        \YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;
        $days = (int)\Setting::get('shop.trade.receive');

        if (!$days) {
            return;
        }
        $orders = \app\backend\modules\order\models\Order::waitReceive()->where('auto_receipt', 0)->where('send_time', '<', (int)Carbon::now()->addDays(-$days)->timestamp)->normal()->get();
        if (!$orders->isEmpty()) {
            $orders->each(function ($order) {
                try {
                    OrderService::orderReceive(['order_id' => $order->id]);
                } catch (\Exception $e) {

                }
            });
        }
    }

    /**
     * 自动关闭订单
     * {@inheritdoc}
     */
    public static function autoClose($uniacid)
    {
        \YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;
        $days = (int)\Setting::get('shop.trade.close_order_days');
        if (!$days) {
            return;
        }
        $orders = \app\backend\modules\order\models\Order::waitPay()->where('create_time', '<', (int)Carbon::now()->addDays(-\Setting::get('shop.trade.close_order_days'))->timestamp)->normal()->get();
        if (!$orders->isEmpty()) {
            $orders->each(function ($order) {
                //dd($order->send_time);
                OrderService::orderClose(['order_id' => $order->id]);
            });
        }
    }

    /**
     * @param $order
     * @throws AppException
     */
    public static function fixVirtualOrder($order)
    {
        \YunShop::app()->uniacid = $order['uniacid'];
        \Setting::$uniqueAccountId = $order['uniacid'];

        if ($order['status'] == 1) {
            OrderService::orderSend(['order_id' => $order['id']]);
        }
        if ($order['status'] == 2) {
            OrderService::orderReceive(['order_id' => $order['id']]);
        }
    }
}