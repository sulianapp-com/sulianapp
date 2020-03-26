<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/11
 * Time: 11:11
 */

namespace app\backend\modules\charts\modules\order\controllers;


use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\Order;
use app\common\services\ExportService;
use Illuminate\Support\Facades\DB;

class TransactionAmountController extends ChartsController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function count()
    {
        $waitPayOrder = 0.00;
        $waitSendOrder = 0.00;
        $waitReceiveOrder = 0.00;
        $completedOrder = 0.00;
        $search = \YunShop::request()->search;
        $orderModel  = Order::uniacid();
        if ($search['is_time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
            $orderModel->whereBetween('created_at', [$searchTime['start'], $searchTime['end']]);
        }
        $orderData = $orderModel->selectRaw('sum(if(plugin_id=31,price,0)) as cashier, sum(if(plugin_id=32,price,0)) as store, sum(if(is_plugin=1,price,0)) as supplier, sum(if(is_plugin=0 && plugin_id=0,price,0)) as shop, status')
            ->groupBy('status')->get();
        $totalOrder = [
            'cashier' => $orderData->sum('cashier'),
            'store' => $orderData->sum('store'),
            'supplier' => $orderData->sum('supplier'),
            'shop' => $orderData->sum('shop'),
        ];

        foreach ($orderData as $order)
        {
            switch ($order['status']) {
                case 0:$waitPayOrder = $order;break;
                case 1:$waitSendOrder = $order;break;
                case 2:$waitReceiveOrder = $order;break;
                case 3:$completedOrder = $order;break;
                default : break;
            }
        }
        return view('charts.order.transaction_amount', [
            'waitPayOrder' => $waitPayOrder,
            'waitSendOrder' => $waitSendOrder,
            'waitReceiveOrder' => $waitReceiveOrder,
            'completedOrder' => $completedOrder,
            'totalOrder' => $totalOrder,
            'search' => $search,
        ])->render();
    }

    public function export()
    {
        $search = \YunShop::request()->search;
        $orderModel  = Order::uniacid();
        if ($search['is_time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
            $orderModel->whereBetween('created_at', [$searchTime['start'], $searchTime['end']]);
        }
        $orderModel = $orderModel->selectRaw('sum(if(plugin_id=31,price,0)) as cashier, sum(if(plugin_id=32,price,0)) as store, sum(if(is_plugin=1,price,0)) as supplier, sum(if(is_plugin=0 && plugin_id=0,price,0)) as shop, status')
            ->groupBy('status');
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($orderModel, $export_page);
        $file_name = date('YmdHis', time()).'交易额统计导出';
        $export_data[0] = ['状态', '商城', '供应商', '门店', '收银台'];
        foreach ($export_model->builder_model as $key => $item) {
            switch ($item->status) {
                case 0:
                    $export_data[$key + 1] = [
                        '待支付订单',
                        $item->shop,
                        $item->supplier,
                        $item->store,
                        $item->cashier,
                    ];
                    break;
                case 1:
                    $export_data[$key + 1] = [
                        '待发货订单',
                        $item->shop,
                        $item->supplier,
                        $item->store,
                        $item->cashier,
                    ];
                    break;
                case 2:
                    $export_data[$key + 1] = [
                        '待收货订单',
                        $item->shop,
                        $item->supplier,
                        $item->store,
                        $item->cashier,
                    ];
                    break;
                case 3:
                    $export_data[$key + 1] = [
                        '已完成订单',
                        $item->shop,
                        $item->supplier,
                        $item->store,
                        $item->cashier,
                    ];
                    break;
                default : break;
            }
        }
        $export_data[] = [
            '总交易额统计',
            $export_model->builder_model->sum('shop'),
            $export_model->builder_model->sum('supplier'),
            $export_model->builder_model->sum('store'),
            $export_model->builder_model->sum('cashier'),
        ];
        $export_model->export($file_name, $export_data, 'charts.order.transaction-amount.count');
        return true;

    }

}