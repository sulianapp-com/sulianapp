<?php

namespace app\backend\modules\charts\modules\finance\controllers;

use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\Balance;
use app\common\services\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/13
 * Time: 15:51
 */
class BalanceController extends ChartsController
{
    protected $time = array();
    protected $balanceLog;

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $searchTime = [];
        $allBalanceData = [];
        $balanceUseData = [];
        $balanceUsedData = [];
        $balanceWithdrawData = [];
        $balanceGivenData = [];
        $balanceRechargeData = [];
        $balanceMemberRechargeData = [];
        $balanceIncomeData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
        }
        $balanceTime = $this->getBalanceTime($searchTime);

        foreach ($this->time as $key => $time) {
            $allBalanceData[$key] = Balance::uniacid()
                ->selectRaw('sum(change_money) as useBalance, sum(if(service_type=7,change_money,0)) as incomeBalance, sum(if(service_type=6,change_money,0)) as withdrawBalance, sum(if(service_type=2,change_money,0)) as usedBalance')
                ->selectRaw('sum(if(service_type=1 ,change_money,0)) as recharge, sum(if(service_type=1 ,change_money,0)) as memberRecharge')
                ->where('created_at','<=', strtotime($time))
                ->first()
                ->toArray();
//            $balanceGivenData[$key] = $allBalanceData[$key]['givenBalance'];
            $balanceUseData[$key] = $allBalanceData[$key]['useBalance'];
            $balanceIncomeData[$key] = $allBalanceData[$key]['incomeBalance'];
            $balanceRechargeData[$key] = $allBalanceData[$key]['recharge'];
            $balanceMemberRechargeData[$key] = $allBalanceData[$key]['memberRecharge'];
            $balanceWithdrawData[$key] = $allBalanceData[$key]['withdrawBalance'] * -1;
            $balanceUsedData[$key] = $allBalanceData[$key]['usedBalance'] * -1;
            $allBalanceData[$key]['date'] = $time;
        }
        krsort($allBalanceData);
        $end = count($allBalanceData) -1;
        return view('charts.finance.balance',[
            'search' => $search,
//            'balanceGivenCount' => $allBalanceData[6]['givenBalance'],
            'balanceUseCount' => $allBalanceData[$end]['useBalance'],
            'balanceWithdrawCount' => $allBalanceData[$end]['withdrawBalance'],
            'balanceIncomeCount' => $allBalanceData[$end]['incomeBalance'],
            'balanceRechargeCount' => $allBalanceData[$end]['recharge'],
            'balanceMemberRechargeCount' => $allBalanceData[$end]['memberRecharge'],
            'balanceUsedCount' => $allBalanceData[$end]['usedBalance'],
            'balanceTime' => json_encode($balanceTime),
            'balanceUseData' => json_encode($balanceUseData),
            'balanceUsedData' => json_encode($balanceUsedData),
            'balanceWithdrawData' => json_encode($balanceWithdrawData),
            'balanceIncomeData' => json_encode($balanceIncomeData),
            'balanceRechargeData' => json_encode($balanceRechargeData),
            'balanceMemberRechargeData' => json_encode($balanceMemberRechargeData),
            'AllBalanceData' => $allBalanceData,
        ])->render();
    }

    public function getBalanceTime($searchTime = null)
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
        $allBalanceData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
        } else {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::now()->startOfDay()->timestamp;
        }

        $this->getBalanceTime($searchTime);

        $builder = Balance::uniacid()
            ->selectRaw('sum(change_money) as useBalance, sum(if(service_type=7,change_money,0)) as incomeBalance, sum(if(service_type=6,change_money,0)) as withdrawBalance, sum(if(service_type=2,change_money,0)) as usedBalance')
            ->selectRaw('sum(if(service_type=1 && operator_id=1,change_money,0)) as recharge, sum(if(service_type=1 && operator=-2,change_money,0)) as memberRecharge')
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"));

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '余额统计导出';
        $export_data[0] = ['时间', '可使用余额','已消耗余额','已提现余额','收入余额','后台充值余额','会员充值余额'];
        foreach ($this->time as $key => $time) {
            $allBalanceData[$key] = Balance::uniacid()
                ->selectRaw('sum(change_money) as useBalance, sum(if(service_type=7,change_money,0)) as incomeBalance, sum(if(service_type=6,change_money,0)) as withdrawBalance, sum(if(service_type=2,change_money,0)) as usedBalance')
                ->selectRaw('sum(if(service_type=1 && operator_id=1,change_money,0)) as recharge, sum(if(service_type=1 && operator=-2,change_money,0)) as memberRecharge')
                ->where('created_at','<=', strtotime($time))
                ->first()
                ->toArray();
            $export_data[$key + 1] = [
                $time,
                $allBalanceData[$key]['useBalance'],
                ($allBalanceData[$key]['usedBalance'] * -1),
                ($allBalanceData[$key]['withdrawBalance'] * -1),
                $allBalanceData[$key]['incomeBalance'],
                $allBalanceData[$key]['recharge'],
                $allBalanceData[$key]['memberRecharge'],
            ];
        }

        $export_model->export($file_name, $export_data, 'charts.finance.point.index');
        return true;
    }

}