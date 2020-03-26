<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 下午5:30
 */

namespace app\backend\modules\point\controllers;


use app\backend\modules\point\models\RechargeModel;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class RechargeRecordsController extends BaseController
{
    /**
     * @var string
     */
    private $page;

    /**
     * @var array
     */
    private $search;

    /**
     * @var RechargeModel
     */
    protected $rechargeModel;

    public function preAction()
    {
        parent::preAction();

        $this->search = $this->getSearch();
        $this->rechargeModel = $this->getMenuModels();
        $this->page = $this->getPage();
    }

    public function index()
    {
        $data = $this->getResultData();

        //dd($this->search);
        return view('point.rechargeRecords', $data);
    }

    /**
     * @return array
     */
    private function getResultData()
    {
        return [
            'pageList'  => $this->rechargeModel,
            'page'      => $this->page,
            'search'    => $this->search
        ];
    }

    /**
     * @return RechargeModel
     */
    private function getMenuModels()
    {
        $rechargeModel = new RechargeModel();

        if ($this->search) {
            $rechargeModel = $rechargeModel->search($this->search)->searchMember($this->search);
        }
        return $rechargeModel->withMember()->orderBy('updated_at','desc')->paginate();
    }

    /**
     * @return string
     */
    private function getPage()
    {
        return PaginationHelper::show($this->rechargeModel->total(),$this->rechargeModel->currentPage(),$this->rechargeModel->perPage());
    }

    /**
     * @return array
     */
    private function getSearch()
    {
        return \YunShop::request()->search;
    }
}
