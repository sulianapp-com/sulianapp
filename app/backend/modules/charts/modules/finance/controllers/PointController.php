<?php

namespace app\backend\modules\charts\modules\finance\controllers;

use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\PointLog;
use app\common\services\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/13
 * Time: 15:51
 */
class PointController extends ChartsController
{
    protected $time = array();
    protected $pointLog;

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $searchTime = [];
        $allPointData = [];
        $pointUseData = [];
        $pointUsedData = [];
        $pointGivenData = [];
        $pointRechargeData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
        }

        $pointTime = $this->getPointTime($searchTime);

        foreach ($this->time as $key => $time) {
            $allPointData[$key] = PointLog::uniacid()->selectRaw('sum(if(point_income_type=1 && point_mode!=5,point,0)) as givenPoint, sum(point) as usePoint, sum(if(point_mode=6,point,0)) as usedPoint, sum(if(point_mode=5,point,0)) as recharge')
                ->where('created_at','<=', strtotime("$time 23:59:59"))
                ->first()
                ->toArray() ;
            $pointGivenData[$key] = $allPointData[$key]['givenPoint'];
            $pointUseData[$key] = $allPointData[$key]['usePoint'];
            $pointRechargeData[$key] = $allPointData[$key]['recharge'];
            $pointUsedData[$key] = $allPointData[$key]['usedPoint'] * -1;
            $allPointData[$key]['date'] = $time;
        }
        $end = count($allPointData) -1;
        krsort($allPointData);
        return view('charts.finance.point',[
            'search' => $search,
            'pointGivenCount' => $allPointData[$end]['givenPoint'],
            'pointUseCount' => $allPointData[$end]['usePoint'],
            'pointUsedCount' => $allPointData[$end]['usedPoint'],
            'pointRechargeCount' => $pointRechargeData[$end]['recharge'],
            'pointTime' => json_encode($pointTime),
            'pointUseData' => json_encode($pointUseData),
            'pointUsedData' => json_encode($pointUsedData),
            'pointGivenData' => json_encode($pointGivenData),
            'pointRechargeData' => json_encode($pointRechargeData),
            'allPointData' => $allPointData,
        ])->render();
    }

    public function getPointTime($searchTime = null)
    {
//        $count = 6;
        if ($searchTime) {
            $count = Carbon::createFromTimestamp($searchTime['end'])->diffInDays(Carbon::createFromTimestamp($searchTime['start']), true);
            while($count >= 0)
            {
                $this->time[] = Carbon::createFromTimestamp($searchTime['end'])->subDay($count)->startOfDay()->format('Y-m-d');
                $count--;
            }
        } else {
            $this->time = [
                Carbon::now()->subDay(6)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(5)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(4)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(3)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(2)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(1)->startOfDay()->format('Y-m-d'),
                Carbon::now()->startOfDay()->format('Y-m-d'),
            ];
        }
        return $this->time;
    }

    /**
     * 导出Excel
     */
    public function export()
    {
        $searchTime = [];
        $allPointData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
        } else {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::now()->startOfDay()->timestamp;
        }

        $this->getPointTime($searchTime);

        $builder = PointLog::uniacid()->selectRaw('sum(if(point_income_type=1 && point_mode!=5,point,0)) as givenPoint, sum(point) as usePoint, sum(if(point_mode=6,point,0)) as usedPoint, sum(if(point_mode=5,point,0)) as recharge')
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"));

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '积分统计导出';
        $export_data[0] = ['时间', '可使用积分', '已消耗积分', '已赠送积分', '充值积分'];
        foreach ($this->time as $key => $time) {
            $allPointData[$key] = PointLog::uniacid()->selectRaw('sum(if(point_income_type=1 && point_mode!=5,point,0)) as givenPoint, sum(point) as usePoint, sum(if(point_mode=6,point,0)) as usedPoint, sum(if(point_mode=5,point,0)) as recharge')
                ->where('created_at','<=', strtotime("$time 23:59:59"))
                ->first()
                ->toArray();
            $export_data[$key + 1] = [
                $time,
                $allPointData[$key]['usePoint'],
                ($allPointData[$key]['usedPoint'] * -1),
                $allPointData[$key]['givenPoint'],
                $allPointData[$key]['recharge'],
            ];
        }

        $export_model->export($file_name, $export_data, 'charts.finance.point.index');
        return true;
    }

}