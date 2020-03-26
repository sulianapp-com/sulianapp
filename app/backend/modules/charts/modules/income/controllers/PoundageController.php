<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/11/14
 * Time: 9:05
 */

namespace app\backend\modules\charts\modules\income\controllers;


use app\backend\modules\charts\models\Withdraw;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PoundageController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $search = \YunShop::request()->search;
        $list = Withdraw::search($search)
            ->where('status', 2)
            ->selectRaw("FROM_UNIXTIME(created_at,'%Y-%m-%d') as date, sum(actual_poundage) as poundage, sum(actual_servicetax) as servicetax")
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"))
            ->orderBy('date','decs')
            ->paginate(10);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        $total = Withdraw::search($search)->where('status', 2)->selectRaw("sum(actual_poundage) as poundage, sum(actual_servicetax) as servicetax")->first();
        return view('charts.income.poundage',[
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
            'total' => $total,
        ])->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function detail()
    {
        $date = request()->date;
        $search = \YunShop::request()->search;
        $start_time = strtotime($date);
        $end_time = Carbon::createFromTimestamp($start_time)->addDay()->timestamp - 1;
        $list = Withdraw::search($search)->where('status', 2)->whereRaw('(actual_poundage + actual_servicetax) > 0')->whereBetween('created_at', [$start_time, $end_time])->paginate(10);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.income.poundage_detail',[
            'types' => Withdraw::getTypes(),
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
        ])->render();
    }

    public function export()
    {
        $search = \YunShop::request()->search;
        $builder = Withdraw::search($search)
            ->where('status', 2)
            ->selectRaw("FROM_UNIXTIME(created_at,'%Y-%m-%d') as date, sum(actual_poundage) as poundage, sum(actual_servicetax) as servicetax")
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"));
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '手续统计导出';
        $export_data[0] = ['时间', '手续费','劳务税','总计'];
        foreach ($builder->orderBy('date','decs')->get() as $key => $item) {
            $export_data[$key+1] = [
                $item->date,
                $item->poundage ?: '0.00',
                $item->servicetax ?: '0.00',
                sprintf("%01.2f",($item->poundage + $item->servicetax)),
            ];
        }
        $export_model->export($file_name, $export_data, 'charts.income.poundage.index');
        return true;
    }

}