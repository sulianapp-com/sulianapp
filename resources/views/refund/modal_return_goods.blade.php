@extends('refund.modal_base')

@section('operation_pass')
    @if($order['has_one_refund_apply']['status'] == \app\common\models\refund\RefundApply::WAIT_CHECK)
        <label class='radio-inline' style="float: left;margin-left: 0px;margin-right: 10px;">
            <input type='radio' value='3' class="refund-action" data-action="{{yzWebUrl('refund.operation.pass')}}"
                   name='refund_status' @if( $order['has_one_refund_apply']['status']=='3' ||
                                        $refund['status']=='4') checked @endif>通过申请(需客户寄回商品)
        </label>
    @endif
@endsection
@section('operation_consensus')
    <label class='radio-inline' style="float: left;margin-left: 0px;margin-right: 10px;">
        <input type='radio' value='1' class='refund-action' class="refund-action"
               data-action='{{yzWebUrl("refund.pay")}}' name='refund_status'>
        同意退款
            @if($order['has_one_refund_apply']['status'] == \app\common\models\refund\RefundApply::WAIT_CHECK)
                (无需客户发货直接退款)
            @elseif($order['has_one_refund_apply']['status'] == \app\common\models\refund\RefundApply::WAIT_RECEIVE_RETURN_GOODS)
                (您已经收到客户寄出的快递)
            @endif

    </label>

    <label class='radio-inline'>
        <input type='radio' value='2' class="refund-action" data-action="{{yzWebUrl('refund.operation.consensus')}}"
               name='refund_status'>手动退款
    </label>

    <div class="help-group" style="display: none;">
        <span class="help-block">微信支付方式： 会返回到相应的的支付渠道(如零钱或银行卡）</span>
        <span class="help-block">支付宝支付方式： 会返回到相应的的支付渠道</span>
        <span class="help-block">其他支付方式： 会返回到微信钱包(需商户平台余额充足)</span>
        <span class="help-block">如有余额抵扣： 会返回金额到商城用户余额</span>
        {{--{if $plugin_commission && $cset['deduction']}--}}
        {{--<span class="help-block">如有佣金抵扣： 会返回佣金到商城用户佣金</span>--}}
        {{--{/if}--}}
        <span class="help-block">手动退款： 订单会完成退款处理，您用其他方式进行退款</span>
    </div>
@endsection