@extends('refund.modal_base')
@section('operation_consensus')
    <label class='radio-inline' style="float: left;margin-left: 0px;margin-right: 10px;">
        <input type='radio' value='1' class='refund-action' class="refund-action"
               data-action='{{yzWebUrl("refund.pay")}}' name='refund_status'>
        同意退款
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