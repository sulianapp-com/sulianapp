<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/31
 * Time: 上午11:28
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Income;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class IncomeController extends BaseController
{
    public function index()
    {

        $pageSize = 20;

        $search = \YunShop::request()->search;


        $incomeModel = Income::getIncomeList($search);
        
        $list = $incomeModel->paginate($pageSize)->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);


        return view('finance.income.income-list', [
            'list' => $list,
            'pager' => $pager,
        ])->render();
    }
}