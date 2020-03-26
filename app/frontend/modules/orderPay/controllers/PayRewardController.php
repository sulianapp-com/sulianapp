<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-12-20
 * Time: 09:44
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace app\frontend\modules\orderPay\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Order;
use app\common\models\OrderPay;

class PayRewardController extends ApiController
{
    public function index()
    {
        $order_sn = request()->order_sn;

        if (empty($order_sn)) {
            return $this->errorJson('请传入正确参数');
        }

        $order_ids = OrderPay::where('pay_sn', $order_sn)->pluck('order_ids')->toArray()[0];

        $orders = Order::whereIn('id', $order_ids)->get();

        if (empty($orders)) {
            return $this->errorJson('订单不存在');
        }

        $price = 0;
        foreach ($orders as $order) {
            $price += $this->getChangeValue($order);
        }

        if (!app('plugins')->isEnabled('pay-reward')) {
            return $this->errorJson('支付奖励插件未开启，请开启插件');
        }

        $set = Setting::get('plugin.pay_reward');

        $result['title'] = $set['title'];
        $result['price'] = $price;

        return $this->successJson('ok', $result);
    }

    private function getChangeValue($order)
    {
        $orderGoods = $order->hasManyOrderGoods;

        $change_value = 0;
        foreach ($orderGoods as $goodsModel)
        {
            $goodsSaleModel = $goodsModel->hasOneGoods->hasOneSale;

            if (!$goodsSaleModel || empty($goodsSaleModel->award_balance)) {
                continue;
            }

            $change_value += $this->proportionMath($goodsModel->payment_amount, $goodsSaleModel->award_balance, $goodsModel->total);
        }

        return $change_value;
    }

    private function proportionMath($price, $proportion, $total)
    {
        if (strexists($proportion, '%')) {
            $proportion = str_replace('%', '', $proportion);

            return bcdiv(bcmul($price,$proportion,4),100,2);
        }

        return bcmul($proportion,$total,2);
    }
}