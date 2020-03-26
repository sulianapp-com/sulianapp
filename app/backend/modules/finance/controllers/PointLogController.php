<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午11:44
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\backend\modules\finance\models\PointLog as PoinLogModel;
use app\common\helpers\PaginationHelper;
use app\Jobs\PointQueueJob;

class PointLogController extends BaseController
{
    public function index(\Illuminate\Http\Request $request)
    {
        $pageSize = 10;
        $search = $request->search;
        $builer = PoinLogModel::getPointLogList($search);
        if ($request->member_id) {
            $builer = $builer->where('member_id', $request->member_id);
        }
        $list = $builer->paginate($pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('finance.point.point_log', [
            'list'          => $list,
            'pager'         => $pager,
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList(),
            'search'        => $search
        ])->render();
    }

    public function test()
    {
        (new PointQueueJob(\YunShop::app()->uniacid))->handle();
        dd('执行成功');
        exit;
    }
}