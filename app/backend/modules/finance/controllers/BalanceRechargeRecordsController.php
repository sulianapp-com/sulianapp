<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/12/13 下午2:25
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\finance\models\BalanceRechargeRecords;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;


class BalanceRechargeRecordsController extends BaseController
{

    public function index()
    {
        $records = BalanceRechargeRecords::records();

        $search = \YunShop::request()->search;
        if ($search) {
            $records = $records->search($search);
        }

        $recordList = $records->orderBy('created_at', 'desc')->paginate();

        //dd($recordList);
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        //支付类型：1后台支付，2 微信支付 3 支付宝， 4 其他支付
        return view('finance.balance.rechargeRecord', [
            'shopSet'       => Setting::get('shop.member'),
            'recordList'    => $recordList,
            'page'          => $pager,
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList(),
            'search'        => $search
        ])->render();
    }

}
