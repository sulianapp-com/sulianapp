<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 11:52
 */

namespace app\backend\modules\charts\modules\income\controllers;

use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\backend\modules\charts\models\Order;
use app\common\models\order\OrderPluginBonus;
use app\common\services\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;

class ShopIncomeStatisticsController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {

        $pageSize = 10;
        $search = \YunShop::request()->search;
        $list = OrderIncomeCount::search($search)
            ->where('status', 3)
            ->selectRaw('day_time, sum(undividend) as undividend, sum(price) as price, sum(cost_price) as cost_price')
            ->selectRaw('sum(store) as store, sum(cashier) as cashier, sum(supplier) as supplier')
            ->groupBy('day_time')
            ->orderBy('day_time', 'desc')
            ->paginate($pageSize);

        $total = OrderIncomeCount::search($search)
            ->where('status', 3)
            ->selectRaw('sum(undividend) as undividend, sum(price) as price, sum(cost_price) as cost_price')
            ->selectRaw('sum(store) as store, sum(cashier) as cashier, sum(supplier) as supplier')
            ->first();

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.income.shop_income_statistics',[
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
            'total' => $total,
        ])->render();
    }

    public function export()
    {
        $search = \YunShop::request()->search;
        $builder = OrderIncomeCount::search($search)
            ->where('status', 3)
            ->selectRaw('day_time, sum(undividend) as undividend, sum(price) as price, sum(cost_price) as cost_price')
            ->selectRaw('sum(store) as store, sum(cashier) as cashier, sum(supplier) as supplier')
            ->groupBy('day_time')
            ->orderBy('day_time', 'desc');
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '订单收益列表导出';
        $export_data[0] = ['日期','订单金额','未被分润','商城收益','供应商收益','门店收益','收银台收益','总收益'];
        foreach ($export_model->builder_model as $key => $item) {
            $export_data[$key+1] = [
                date('Y-m-d', $item->day_time),
                $item->price ?: '0.00',
                $item->undividend,
                sprintf("%01.2f",($item->price - $item->cost_price) > 0 ? $item->price - $item->cost_price : '0.00'),
                $item->supplier ?: '0.00',
                $item->store ?: '0.00',
                $item->cashier ?: '0.00',
                sprintf("%01.2f",$item->price - $item->cost_price + $item->supplier + $item->store + $item->cashier) ?: '0.00'
            ];
        }
        $export_model->export($file_name, $export_data, 'charts.income.shop-income-statistics.index');
        return true;
    }
}