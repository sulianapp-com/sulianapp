<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/31
 * Time: 9:42 PM
 */

namespace app\backend\modules\point\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\finance\PointQueueLog;

class QueueLogController extends BaseController
{
    public function index()
    {
        $search = request()->search;
        $list = PointQueueLog::getList($search)
            ->orderBy('id', 'desc')
            ->paginate();
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('point.queueLog', [
            'list' => $list,
            'pager' => $pager,
            'search' => $search
        ]);
    }
}