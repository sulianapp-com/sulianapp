<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/15 上午9:51
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\income\controllers;


use app\backend\modules\income\models\Income;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class IncomeRecordsController extends BaseController
{

    //收入明细
    public function index()
    {
        $records = Income::records()->withMember();

        $search = \YunShop::request()->search;
        if ($search) {
            //dd($search);
            $records = $records->search($search)->searchMember($search);
        }

        $pageList = $records->orderBy('created_at','desc')->paginate();
        $page = PaginationHelper::show($pageList->total(),$pageList->currentPage(),$pageList->perPage());

        return view('income.income_records',[
            'pageList'          => $pageList,
            'page'              => $page,
            'search'            => $search,
            'income_type_comment' => $this->getIncomeTypeComment()
        ])->render();

    }


    private function getIncomeTypeComment()
    {
        return \app\backend\modules\income\Income::current()->getItems();
    }




}
