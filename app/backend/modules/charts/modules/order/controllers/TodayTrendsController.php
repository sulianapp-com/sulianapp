<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/17 下午3:02
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\order\controllers;


use app\backend\modules\order\models\Order;
use app\common\components\BaseController;

class TodayTrendsController extends BaseController
{
    public function index()
    {
        //dd($this->getData());
        return view('charts.order.today_trends', $this->getData())->render();
    }


    private function getData()
    {
        $charts_data = [];
        $statistics_data = [];
        foreach ($this->timeData(date('H')) as $item) {

            $time = $this->createTime($item);

            $created_order  = $this->getTodayOrderCount('0', $time, $time + 7200);
            $pay_order      = $this->getTodayOrderCount('1', $time, $time + 7200);
            $received_order = $this->getTodayOrderCount('2', $time, $time + 7200);


            $statistics_data[] = $this->statisticsData($item,$created_order,$pay_order,$received_order);

            $charts_data['created_order'][] = $created_order;
            $charts_data['pay_order'][] = $pay_order;
            $charts_data['received_order'][] = $received_order;
        }
        return ['charts_data' => json_encode($charts_data), 'statistics_data' => json_encode($statistics_data)];
    }

    private function createTime($hour)
    {
        return mktime((int)$hour,0,0,date('m'),date('d'),date('Y'));
    }


    private function statisticsData($time, $created_order, $pay_order, $received_order)
    {
        return [
            'time'          => $time,
            'created_order' => $created_order,
            'pay_order'     => $pay_order,
            'received_order'=> $received_order,
        ];
    }


    private function getTodayOrderCount($status, $start_time, $end_time)
    {
        return Order::uniacid()->where('status', $status)->whereBetween('created_at', [$start_time,$end_time])->count();
    }



    private function timeData($hour = 23)
    {
        $data = [];
        for ($i = 0; $i <= $hour; $i += 2) {
            $data[] = $i . ":00";
        }
        return $data;
    }


}
