<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/31
 * Time: 9:42 PM
 */

namespace app\backend\modules\point\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\finance\PointQueue;

class QueueController extends BaseController
{
    public function index()
    {
        $searh = request()->search;
        $list = PointQueue::getList($searh)
            ->orderBy('id', 'desc')
            ->paginate();
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('point.queue', [
            'list' => $list,
            'pager' => $pager,
            'search' => $searh
        ]);
    }
}