<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/15
 * Time: 下午12:02
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;

class PayFailListController extends BaseController
{
    public function index(){
        $orderIds = DB::table('yz_order as o')->join('yz_order_pay_order as opo', 'o.id', '=', 'opo.order_id')
            ->join('yz_order_pay as op', 'op.id', '=', 'opo.order_pay_id')
            ->where('o.status',0)
            ->where('op.status',1)
            ->pluck('o.id');

    }
}