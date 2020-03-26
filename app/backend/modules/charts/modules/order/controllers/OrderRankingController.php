<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/17 下午3:20
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\order\controllers;


use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\OrderStatistics;
use app\common\helpers\PaginationHelper;

class OrderRankingController extends ChartsController
{
    public function count()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;

        $list = OrderStatistics::getMember($search)->orderBy('total_quantity', 'desc')->paginate($pageSize);
        $page = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.order.order_ranking', [
            'list' => $list,
            'page' => $page,
            'search' => $search,
        ])->render();
    }

    public function money()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;

        $list = OrderStatistics::getMember($search)->orderBy('total_amount', 'desc')->paginate($pageSize);
        $page = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.order.order_ranking', [
            'list' => $list,
            'page' => $page,
            'search' => $search,
        ])->render();
    }

}
