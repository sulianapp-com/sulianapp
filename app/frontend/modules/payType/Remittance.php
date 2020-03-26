<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/8
 * Time: 下午3:58
 */

namespace app\frontend\modules\payType;

use app\common\modules\payType\remittance\models\flows\RemittanceFlow;

class Remittance extends BasePayType
{
    /**
     * @throws \Exception
     */
    public function applyPay()
    {
        $flow = RemittanceFlow::first();
        //$this->orderPay->pending();
        $this->orderPay->addProcess($flow);
        // 转账流程
//        $this->orderPay->currentProcess()->AfterCompleted(
//            function ($process) {
//                //$this->orderPay->unPending();
//                // 订单支付
//                $this->orderPay->pay();
//            }
//
//        );
    }
}
