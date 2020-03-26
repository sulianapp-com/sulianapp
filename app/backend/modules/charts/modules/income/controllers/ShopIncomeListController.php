<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 11:51
 */
namespace app\backend\modules\charts\modules\income\controllers;

use app\backend\modules\charts\models\OrderIncomeCount;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\order\OrderPluginBonus;
use app\common\services\ExportService;

class ShopIncomeListController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        if (!app('plugins')->isEnabled('supplier') && !app('plugins')->isEnabled('store-cashier')) {
            return $this->message('未开启供应商，门店插件，暂不能统计');
        }
        $pageSize = 10;
        $search = \YunShop::request()->search;
        $list = OrderIncomeCount::search($search)
            ->where('status', 3)
            ->select(['order_sn', 'buy_name', 'price', 'shop_name', 'plugin_id', 'undividend', 'cost_price', 'supplier', 'store', 'cashier','order_id'])
            ->orderBy('id', 'desc')
            ->paginate($pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
       
        return view('charts.income.shop_income_list',[
            'list' => $list,
            'pager' => $pager,
            'search' => $search,
        ])->render();
    }

    public function export()
    {
        $search = \YunShop::request()->search;
        $builder = OrderIncomeCount::search($search)
            ->where('status', 3)
            ->select(['order_sn', 'buy_name', 'price', 'shop_name', 'plugin_id', 'undividend', 'cost_price', 'supplier', 'store', 'cashier','order_id'])
            ->orderBy('id', 'desc');
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '订单收益统计导出';
        $export_data[0] = ['订单号', '购买者','订单金额','订单类型','商家','未被分润','商城收益','供应商收益','门店收益','收银台收益'];
        foreach ($export_model->builder_model as $key => $item) {

            if ($item->plugin_id == 1) {
                $type_name = '供应商';
            } elseif ($item->plugin == 31) {
                $type_name = '门店';
            } elseif ($item->plugin == 32) {
                $type_name = '收银台';
            } else {
                $type_name = '商城';
            }
            $export_data[$key+1] = [
                $item->order_sn,
                $item->buy_name ?: '未更新',
                $item->price ?: '0.00',
                $type_name,
                $item->shop_name,
                $item->undividend,
                sprintf("%01.2f",($item->price - $item->cost_price) > 0 ? $item->price - $item->cost_price : '0.00'),
                $item->supplier ?: '0.00',
                $item->store ?: '0.00',
                $item->cashier ?: '0.00',
            ];
        }
        $export_model->export($file_name, $export_data, 'charts.income.shop-income-list.index');
        return true;
    }

}