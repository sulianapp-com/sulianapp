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
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\backend\modules\member\models\Member;

class PointMemberController extends BaseController
{
    public function index()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;
        $memberList = Member::getMembers()->paginate($pageSize);
        if ($search) {
            $memberList = Member::searchMembers(\YunShop::request(), 'credit1')->paginate($pageSize);
        }
        $pager = PaginationHelper::show($memberList->total(), $memberList->currentPage(), $memberList->perPage());

        return view('finance.point.point_member', [
            'search'        => $search,
            'memberList'    => $memberList,
            'pager'         => $pager,
            'transfer_love' => $this->isShowTransferLove(),
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList()
        ])->render();
    }

    private function isShowTransferLove()
    {
        $point_set = Setting::get('point.set');

        if (\YunShop::plugin()->get('love') && $point_set['transfer_love']) {
            return true;
        }
        return false;
    }
}
