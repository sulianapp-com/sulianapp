<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-19
 * Time: 16:52
 */

namespace app\backend\modules\excelRecharge\controllers;


use app\backend\models\excelRecharge\DetailModel;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class DetailController extends BaseController
{
    /**
     * @var DetailModel
     */
    protected $recordsModels;


    //会员excel充值详细记录
    public function index()
    {
        $this->recordsModels = $this->pageList();

        return view('excelRecharge.detail', $this->resultData());
    }

    private function resultData()
    {
        return [
            'page'     => $this->page(),
            'pageList' => $this->recordsModels
        ];
    }

    private function page()
    {
        return PaginationHelper::show($this->recordsModels->total(), $this->recordsModels->currentPage(), $this->recordsModels->perPage());
    }

    /**
     * @return DetailModel
     */
    private function pageList()
    {
        $records = DetailModel::with('member');

        $rechargeId = $this->rechargeIdParam();
        if ($rechargeId) {
            $records->where('recharge_id', $rechargeId);
        }
        return $records->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate('', ['*'], '', $this->pageParam());
    }

    /**
     * @return int
     */
    private function pageParam()
    {
        return (int)request()->page ?: 1;
    }

    /**
     * @return int
     */
    private function rechargeIdParam()
    {
        return (int)request()->recharge_id ?: 1;
    }
}
