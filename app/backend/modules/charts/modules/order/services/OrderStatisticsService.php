<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 19:00
 */

namespace app\backend\modules\charts\modules\order\services;


use app\backend\modules\charts\models\OrderStatistics;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;

class OrderStatisticsService
{
    public function orderStatistics()
    {
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            //全部
            $order_all_count = collect(DB::table('yz_order')->select('uid','uniacid', DB::raw('count(1) as total_quantity'))->groupBy('uid')->where('uniacid', \YunShop::app()->uniacid)->get()->toArray());
            $order_all_money = collect(DB::table('yz_order')->select('uid','uniacid', DB::raw('sum(price) as total_amount'))->groupBy('uid')->where('uniacid', \YunShop::app()->uniacid)->get()->toArray());
            //已支付
            $order_pay_count = collect(DB::table('yz_order')->select('uid','uniacid', DB::raw('count(1) as total_pay_quantity'))->whereIn('status', [1,2,3])->groupBy('uid')->where('uniacid', \YunShop::app()->uniacid)->get()->toArray());
            $order_pay_money = collect(DB::table('yz_order')->select('uid','uniacid', DB::raw('sum(price) as total_pay_amount'))->whereIn('status', [1,2,3])->groupBy('uid')->where('uniacid', \YunShop::app()->uniacid)->get()->toArray());
            //已完成
            $order_complete_count = collect(DB::table('yz_order')->select('uid','uniacid', DB::raw('count(1) as total_complete_quantity'))->where('status', 3)->groupBy('uid')->where('uniacid', \YunShop::app()->uniacid)->get()->toArray());
            $order_complete_money = collect(DB::table('yz_order')->select('uid','uniacid', DB::raw('sum(price) as total_complete_amount'))->where('status', 3)->groupBy('uid')->where('uniacid', \YunShop::app()->uniacid)->get()->toArray());
            $result = $order_all_count->map(function(Collection $order) use($order_all_money,$order_pay_count,$order_pay_money,$order_complete_count,$order_complete_money){
                $order =  $this->mergeOrderData($order,$order_all_money);
                $order =  $this->mergeOrderData($order,$order_pay_count);
                $order =  $this->mergeOrderData($order,$order_pay_money);
                $order =  $this->mergeOrderData($order,$order_complete_count);
                $order =  $this->mergeOrderData($order,$order_complete_money);
                return $order;
            });
//        dd($result);
            foreach ($result as $item) {
                OrderStatistics::updateOrCreate(['uid' => $item['uid']], $item);
            }
        }
    }

    private function mergeOrderData($order,$mergeOrders){
        $data = $mergeOrders->where('uid',$order['uid'])->first();
        if($data){
            $order = collect($order)->merge($data);
        }
        return $order;
    }
}