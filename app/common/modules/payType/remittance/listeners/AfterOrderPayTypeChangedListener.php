<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/28
 * Time: 下午6:04
 */

namespace app\common\modules\payType\remittance\listeners;


use app\common\modules\payType\events\AfterOrderPayTypeChangedEvent;
use app\common\exceptions\AppException;
use app\common\models\PayType;
use app\common\modules\payType\remittance\models\flows\RemittanceFlow;

class AfterOrderPayTypeChangedListener
{
    /**
     * @param AfterOrderPayTypeChangedEvent $event
     * @throws AppException
     */
    public function handle(AfterOrderPayTypeChangedEvent $event)
    {
        $order = $event->getOrder();


        if ($order->getOriginal('pay_type_id') == PayType::REMITTANCE) {

            if(is_null($order->hasOneOrderPay->currentProcess())){
                return;
            }

            if($order->hasOneOrderPay->currentProcess()->status->code == RemittanceFlow::STATE_WAIT_RECEIPT){
                throw new AppException("订单(id:{$order->id})的转账记录已提交转账审核,请等待处理完成或取消申请后,再尝试其他支付方式");
            }

        }
    }
}