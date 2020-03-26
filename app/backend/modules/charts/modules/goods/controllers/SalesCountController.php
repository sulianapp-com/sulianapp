<?php

namespace app\backend\modules\charts\modules\goods\controllers;


use app\common\components\BaseController;

class SalesCountController extends BaseController
{

    public function index()
    {
        //dd(123);
        return view('charts.goods.sales_count')->render();
    }



}
