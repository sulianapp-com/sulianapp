<?php

namespace app\backend\modules\charts\modules\finance\controllers;

use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\CouponLog;
use app\common\models\Coupon;
use app\common\services\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/13
 * Time: 15:51
 */
class CouponController extends ChartsController
{
    protected $time = array();
    protected $couponLog;


    public function preAction()
    {
        $this->couponLog = new CouponLog();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $searchTime = [];
        $allCouponLogData = [];
        $couponUsedData = [];
        $couponGivenData = [];
        $couponExpiredData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
        }
        $couponTime = $this->getCouponTime($searchTime);

        foreach ($this->time as $key => $time) {
            $allCouponLogData[$key] = CouponLog::uniacid()
                ->selectRaw('count(id) as givenCoupon, sum(used) as usedCoupon')
                ->where('get_time','<=', strtotime($time))
                ->first()
                ->toArray();
            $allCouponData[$key] = Coupon::uniacid()
                ->selectRaw('count(id) as expiredCoupon')
                ->where('created_at','<=', strtotime($time))
                ->first()
                ->toArray();
            $couponGivenData[$key] = $allCouponLogData[$key]['givenCoupon'];
            $couponUsedData[$key] = $allCouponLogData[$key]['usedCoupon'];
            $couponExpiredData[$key] = $allCouponData[$key]['expiredCoupon'];
            $allCouponLogData[$key]['date'] = $time;
            $allCouponLogData[$key]['expiredCoupon'] = $allCouponData[$key]['expiredCoupon'];;
        }
        $end = count($allCouponLogData) -1;
        krsort($allCouponLogData);
        return view('charts.finance.coupon',[
            'search' => $search,
            'couponGivenCount' => $allCouponLogData[$end]['givenCoupon'],
            'couponUsedCount' => $allCouponLogData[$end]['usedCoupon'],
            'couponExpiredCount' => $allCouponLogData[$end]['expiredCoupon'],
            'couponTime' => json_encode($couponTime),
            'couponUsedData' => json_encode($couponUsedData),
            'couponGivenData' => json_encode($couponGivenData),
            'couponExpiredData' => json_encode($couponExpiredData),
            'allCouponLogData' => $allCouponLogData,
        ])->render();
    }

    public function getCouponTime($searchTime = null)
    {
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
        $allCouponData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
        } else {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::now()->startOfDay()->timestamp;
        }

        $this->getCouponTime($searchTime);

        $builder = CouponLog::uniacid()
            ->selectRaw('count(id) as givenCoupon, sum(used) as usedCoupon')
            ->groupBy(DB::raw("FROM_UNIXTIME(get_time,'%Y-%m-%d')"));

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '积分统计导出';
        $export_data[0] = ['时间', '已赠送优惠券','已消耗优惠券','已过期优惠券'];
        foreach ($this->time as $key => $time) {
            $allCouponLogData[$key] = CouponLog::uniacid()
                ->selectRaw('count(id) as givenCoupon, sum(used) as usedCoupon')
                ->where('get_time','<=', strtotime($time))
                ->first()
                ->toArray();
            $allCouponData[$key] = Coupon::uniacid()
                ->selectRaw('count(id) as expiredCoupon')
                ->where('created_at','<=', strtotime($time))
                ->first()
                ->toArray();
            $export_data[$key + 1] = [
                $time,
                $allCouponLogData[$key]['givenCoupon'],
                $allCouponLogData[$key]['usedCoupon'],
                $allCouponData[$key]['expiredCoupon'],
            ];
        }

        $export_model->export($file_name, $export_data, 'charts.finance.point.index');
        return true;
    }

}