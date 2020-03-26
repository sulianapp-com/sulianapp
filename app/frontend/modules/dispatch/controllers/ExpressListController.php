<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午2:58
 */

namespace app\frontend\modules\dispatch\controllers;


use app\common\components\ApiController;
use app\common\repositories\ExpressCompany;

class ExpressListController extends ApiController
{
    public function index()
    {
        $expressCompanies = ExpressCompany::create()->all();
        return $this->successJson('成功', ['express_companies' => $expressCompanies]);
    }
}