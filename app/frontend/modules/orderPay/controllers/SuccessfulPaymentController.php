<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/5/15
 * Time: 17:12
 */

namespace app\frontend\modules\orderPay\controllers;


use app\common\components\ApiController;
use app\common\models\OrderPay;
use app\common\models\Order;
use app\common\services\finance\CalculationPointService;
use app\common\listeners\point\PointListener;
use  app\common\models\finance\PointLog;

class SuccessfulPaymentController extends ApiController
{


    /**
     * 支付跳转页面
     */
    public function paymentJump($outtradeno)
    {
        $outtradeno = \YunShop::request()->outtradeno;
        $data = [];
        $data['app_links'] = '';
        $data['integral'] = '';
        /**
         * 判断是余额还是第三方支付
         */
        \Log::debug('rrrrrrrrrrr',$outtradeno);
        if ($outtradeno){
            \Log::debug('66665656',$outtradeno);
            if(preg_match('/^([1-9][0-9]*){1,10}$/',$outtradeno)){
                //余额
                $orderPay = OrderPay::find($outtradeno);
                \Log::debug('余额99999999',$outtradeno);
            }else{
                \Log::debug('判断是余额还是第三方支付',$outtradeno);
                $orderPay = OrderPay::where('pay_sn', $outtradeno)->first();
            }
//            $orders = Order::find($orderPay->order_ids);

            $orders = Order::where('id', $orderPay->order_ids)->with('orderGoods')->first();

            $order_integral = PointListener::getPointDateByOrder($orders);//point

            $integral = PointListener::byGoodsGivePointPay($orders);


//
//            foreach ($orders as $itme){
//                $integral = PointLog::where('order_id',$itme->id)->first();
//                $data['integral'] += $integral['point'];
//            }
            $data['integral'] = bcadd($integral , $order_integral['point'],2);
        }

        if (app('plugins')->isEnabled('app-set')) {
            $set = \Setting::get('shop_app.pay');
            $data['app_links'] = $set['app_links'];
        }

        $data['name'] = \Setting::get('shop.shop.name');

        \Log::debug('请求成功',$data);
        return $this->successJson('请求成功',$data);
    }


}