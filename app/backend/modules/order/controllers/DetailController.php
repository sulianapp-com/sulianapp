<?php
/**
 * 订单详情
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/4
 * Time: 上午11:16
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\member\models\Member;
use app\backend\modules\order\models\Order;
use app\backend\modules\refund\models\RefundApply;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\models\Goods;
use app\common\modules\order\OrderOperationsCollector;
use app\common\services\DivFromService;

use app\common\models\order\Invoice;
class DetailController extends BaseController
{
    public function getMemberButtons()
    {
        $orderStatus = array_keys(app('OrderManager')->setting('status'));
        $buttons = array_map(function ($orderStatus) {
            var_dump($orderStatus);
            $order = Order::where('status', $orderStatus)->orderBy('id', 'desc')->first();
            dump($order->buttonModels);
            dump($order->oldButtonModels);
        }, $orderStatus);
    }

    public function ajax()
    {
        $order = Order::orders()->with(['deductions', 'coupons', 'discounts','orderFees', 'orderPays' => function ($query) {
            $query->with('payType');
        }, 'hasOnePayType']);
        if (request()->has('id')) {
            $order = $order->find(request('id'));
        }
        if (request()->has('order_sn')) {
            $order = $order->where('order_sn', request('order_sn'))->first();
        }
        if (!$order) {
            throw new AppException('未找到订单');
        }
        if (!empty($order->express)) {


            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);

            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }
        return $order->toArray();
    }

    public function express()
    {
//        $express = RefundApply::where('order_id',request('id'))->with('returnExpress')->first();
//        dd($express);

        $order = Order::orders()->with(['deductions', 'coupons', 'discounts','orderFees', 'orderPays' => function ($query) {
            $query->with('payType');
        }, 'hasOnePayType']);
        if (request()->has('id')) {
            $order = $order->find(request('id'));
        }
        if (request()->has('order_sn')) {
            $order = $order->where('order_sn', request('order_sn'))->first();
        }
        if (!$order) {
            throw new AppException('未找到订单');
        }
//        dd($order->hasOneRefundApply->returnExpress);
        if (!empty($order->hasOneRefundApply->returnExpress)) {

            $express = $order->express->getExpress($order->hasOneRefundApply->returnExpress->express_code, $order->hasOneRefundApply->returnExpress->express_sn);

            $dispatch['express_sn'] = $order->hasOneRefundApply->returnExpress->express_sn;
            $dispatch['company_name'] = $order->hasOneRefundApply->returnExpress->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }

        return $this->errorJson('查询成功',$dispatch);
    }

    /**
     * @param \Request $request
     * @return string
     * @throws AppException
     * @throws \Throwable
     */
    public function index(\Illuminate\Http\Request $request)
    {

        $order = Order::orders()->with(['deductions', 'coupons', 'discounts','orderFees', 'orderPays' => function ($query) {
            $query->with('payType');
        }, 'hasOnePayType']);
        if (request()->has('id')) {
            $order = $order->find(request('id'));
        }
        if (request()->has('order_sn')) {
            $order = $order->where('order_sn', request('order_sn'))->first();
        }

        if (!$order) {
            throw new AppException('未找到订单');
        }
        if (!empty($order->express)) {


            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);

            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }
        $trade = \Setting::get('shop.trade');

        foreach ($order['hasManyOrderGoods'] as $key => $order_goods){
            $order['hasManyOrderGoods'][$key]['goods_price'] = bcdiv($order_goods['goods_price'],$order_goods['total']);
            $order['hasManyOrderGoods'][$key]['goods_market_price'] = bcdiv($order_goods['goods_market_price'],$order_goods['total']);
            $order['hasManyOrderGoods'][$key]['goods_cost_price'] = bcdiv($order_goods['goods_cost_price'],$order_goods['total']);
        }
//        dd($order);
        return view('order.detail', [
            'order' => $order ? $order->toArray() : [],
            'invoice_set'=>$trade['invoice'],
            'dispatch' => $dispatch,
            'div_from' => $this->getDivFrom($order),
            'var' => \YunShop::app()->get(),
            'ops' => 'order.ops',
            'edit_goods' => 'goods.goods.edit'
        ])->render();
    }

    private function getDivFrom($order)
    {
        if (!$order || !$order->hasManyOrderGoods) {
            return ['status' => false];
        }
        $goods_ids = [];
        foreach ($order->hasManyOrderGoods as $key => $goods) {
            $goods_ids[] = $goods['goods_id'];
        }

        $memberInfo = Member::select('realname', 'idcard')->where('uid', $order->uid)->first();

        $result['status'] = DivFromService::isDisplay($goods_ids);
        $result['member_name'] = $memberInfo->realname;
        $result['member_card'] = $memberInfo->idcard;

        return $result;
    }


}