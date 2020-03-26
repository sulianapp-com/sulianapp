<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/15
 * Time: 下午3:44
 */

namespace app\backend\modules\payOrder\controllers;

use app\backend\modules\orderPay\fix\DoublePaymentRepair;
use app\backend\modules\payOrder\fix\PaymentCallbackRepair;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\models\OrderPay;
use app\common\models\PayOrder;
use Illuminate\Support\Facades\DB;

class FixController extends BaseController
{
    /**
     * @throws \app\common\exceptions\AppException
     */
    public function callback()
    {
        /**
         * @var PayOrder $payOrder
         */
        $payOrder = PayOrder::find(request('pay_order_id'));
        if (!$payOrder) {
            throw new AppException('未找到支付记录' . request('pay_order_id'));
        }
        $a = (new PaymentCallbackRepair($payOrder))->handle();
        dd($a);
    }
    public function allCallback(){
        $payOrderIds = DB::table('yz_pay_order as po')->join('yz_order_pay as op', 'po.out_order_no', '=', 'op.pay_sn')
            ->where('po.status','2')->where('op.status',0)->distinct()->pluck('po.id');
        $payOrders = PayOrder::whereIn('id',$payOrderIds)->get();
        $payOrders->each(function ($payOrder) {
            if (!$payOrder) {
                throw new AppException('未找到支付记录' . request('pay_order_id'));
            }
            $a = (new PaymentCallbackRepair($payOrder))->handle();
            dump($a);
        });

    }
}