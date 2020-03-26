<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 下午7:40
 */

namespace app\frontend\modules\refund\controllers;


use app\common\components\ApiController;
use app\frontend\modules\refund\models\RefundApply;

class ListController extends ApiController
{
    public function index(\Illuminate\Http\Request $request)
    {
        $this->validate([
            'pagesize' => 'sometimes|filled|integer',
            'page' => 'sometimes|filled|integer',
        ]);
        $refunds = RefundApply::defaults()->paginate($request->query('pagesize', '20'));
        

        return $this->successJson('成功', $refunds->toArray());
    }
}