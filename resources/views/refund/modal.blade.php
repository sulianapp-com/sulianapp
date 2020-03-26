@if($order['has_one_refund_apply']['refund_type'] == \app\common\models\refund\RefundApply::REFUND_TYPE_REFUND_MONEY)
    @include('refund.modal_refund_money')
@elseif($order['has_one_refund_apply']['refund_type'] == \app\common\models\refund\RefundApply::REFUND_TYPE_RETURN_GOODS)
    @include('refund.modal_return_goods')
@elseif($order['has_one_refund_apply']['refund_type'] == \app\common\models\refund\RefundApply::REFUND_TYPE_EXCHANGE_GOODS)
    @include('refund.modal_exchange_goods')
@endif