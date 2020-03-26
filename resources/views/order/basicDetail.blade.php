<style>
    .form-group {
        overflow: hidden;
        margin-bottom: 0 !important;
    }

    .line {
        margin: 10px;
        border-bottom: 1px solid #ddd
    }
</style>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝 :</label>
    <div class="col-sm-9 col-xs-12">
        <img src='{{$order['belongs_to_member']['avatar']}}'
             style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
        <a href="{!! yzWebUrl('member.member.detail',array('id'=>$order['belongs_to_member']['uid'])) !!}"> {{$order['belongs_to_member']['nickname']}}</a>

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员信息 :</label>
    <div class="col-sm-9 col-xs-12">
        <div class='form-control-static'>ID: {{$order['belongs_to_member']['uid']}}
            姓名: {{$order['belongs_to_member']['realname']}} /
            手机号: {{$order['belongs_to_member']['mobile']}}</div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单编号 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">{{$order['order_sn']}} </p>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单金额 :</label>
    <div class="col-sm-9 col-xs-12">
        <div class="form-control-static">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td style='border:none;text-align:right;'>商品小计：</td>
                    <td style='border:none;text-align:right;;'>
                        ￥{{number_format( $order['order_goods_price'] ,2)}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>手续费：</td>
                    <td style='border:none;text-align:right;'>
                        ￥{{number_format( $order['fee_amount'] ,2)}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>优惠：</td>
                    <td style='border:none;text-align:right;'>
                        ￥{{number_format( $order['discount_price'] ,2)}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>抵扣：</td>
                    <td style='border:none;text-align:right;'>
                        - ￥{{number_format( $order['deduction_price'] ,2)}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>运费：</td>
                    <td style='border:none;text-align:right;'>
                        ￥{{number_format( $order['dispatch_price'] ,2)}}</td>
                </tr>
                <tr>
                    <td style='border:none;text-align:right;'>应收款：</td>
                    <td style='border:none;text-align:right;color:green;'>
                        ￥{{number_format($order['price'],2)}}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单状态 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">
                                    <span class="label
                                    @if ($order['status'] == 3) label-success
                                    @elseif ($order['status'] == -1) label-default
                                    @else label-info
                                    @endif">{{$order['status_name']}}</span>
        </p>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付方式 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">

            <span class="label label-info">{{$order['pay_type_name']}}</span>
            @if(count($order['order_pays']))
                <a target="_blank" href="{{yzWebUrl('order.orderPay', array('order_id' => $order['id']))}}" class='btn btn-default'>查看支付记录</a>
                @endif
        </p>

    </div>

</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">上传发票</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('basic-detail[invoice]', $order['invoice']) !!}
    </div>

</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <br/>
        <button name='saveremark' onclick="sub('invoice')" class='btn btn-default'>保存发票</button>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户备注 :</label>
    <div class="col-sm-9 col-xs-12" class="form-control" style="height:150px;" cols="70" >
        <textarea style="height:140px;" class="form-control" cols="70" disabled>{{$order['note']}}</textarea>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商户备注 :</label>
    <div class="col-sm-9 col-xs-12">
        <textarea style="height:150px;" class="form-control" id="remark" name="remark" cols="70">{{$order['has_one_order_remark']['remark']}}</textarea>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 col-xs-12">
        <br/>
        <button name='saveremark' onclick="sub('remark')" class='btn btn-default'>保存备注</button>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单日期 :</label>
    <div class="col-sm-9 col-xs-12">
        <p class="form-control-static">{{$order['create_time']}}</p>
    </div>
</div>
@if ($order['status'] >= 1)
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">付款时间 :</label>
        <div class="col-sm-9 col-xs-12">
            <p class="form-control-static">{{$order['pay_time']}}</p>
        </div>
    </div>
@endif
@if ($order['status'] >= 2)
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">发货时间 :</label>
        <div class="col-sm-9 col-xs-12">
            <p class="form-control-static">{{$order['send_time']}}</p>
        </div>
    </div>
@endif
@if ($order['status'] == 3)
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">完成时间 :</label>
        <div class="col-sm-9 col-xs-12">
            <p class="form-control-static">{{$order['finish_time']}}</p>
        </div>
    </div>
@endif
@if (!empty($order['address']))
    @include('dispatch.detail')
@endif

@section('plugin_order_detail')
    @foreach(\app\common\modules\shop\ShopConfig::current()->get('shop-foundation.order.order_detail') as $plugin_name=>$plugin_class)
        {!! widget($plugin_class['class'], ['order_id'=> $order['id']])!!}
    @endforeach
@show


@if (!empty($order['has_one_refund_apply']))
    @include('refund.index')
@endif
@if(!empty($order['collect_name']))
    @include('invoice.display')
@endif

@if (app('plugins')->isEnabled('delivery-station'))

    {!! (new\Yunshop\DeliveryStation\widgets\OrderWidget())->run($order['id']) !!}

@endif

@if (count($order['discounts']))
    <div class="panel panel-default">
        <div class="panel-heading">
            优惠信息
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th class="col-md-5 col-lg-3">优惠名称</th>
                    <th class="col-md-5 col-lg-3">优惠金额</th>
                </tr>
                </thead>
                @foreach ($order['discounts'] as $discount)
                    <tr>
                        <td>{{$discount['name']}}</td>
                        <td>¥{{$discount['amount']}}</td>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>
@endif
@if (count($order['order_fees']))
    <div class="panel panel-default">
        <div class="panel-heading">
            手续费信息
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th class="col-md-5 col-lg-3">手续费名称</th>
                    <th class="col-md-5 col-lg-3">手续费金额</th>
                </tr>
                </thead>
                @foreach ($order['order_fees'] as $orderFee)
                    <tr>
                        <td>{{$orderFee['name']}}</td>
                        <td>¥{{$orderFee['amount']}}</td>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>
@endif
@if (count($order['deductions']))
    <div class="panel panel-default">
        <div class="panel-heading">
            抵扣信息
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th class="col-md-5 col-lg-3">名称</th>
                    <th class="col-md-5 col-lg-1">抵扣值</th>
                    <th class="col-md-5 col-lg-3">抵扣金额</th>
                </tr>
                </thead>
                @foreach ($order['deductions'] as $deduction)
                    <tr>
                        <td>{{$deduction['name']}}</td>
                        <td>{{$deduction['coin']}}</td>
                        <td>¥{{$deduction['amount']}}</td>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>
@endif
@if (count($order['coupons']))
    <div class="panel panel-default">
        <div class="panel-heading">
            优惠券信息
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <th class="col-md-5 col-lg-3">名称</th>
                    <th class="col-md-5 col-lg-3">优惠金额</th>
                </tr>
                </thead>
                @foreach ($order['coupons'] as $coupon)
                    <tr>
                        <td>{{$coupon['name']}}</td>
                        <td>¥{{$coupon['amount']}}</td>
                    </tr>

                @endforeach
            </table>
        </div>
    </div>
@endif
@if($div_from['status'])
    <div class="panel panel-default">
        <div class="panel-heading">
            个人表单信息
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">
                        {{ $div_from['member_name'] }}
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">身份证 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">
                        {{ $div_from['member_card'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif


<div class="panel panel-default">
    <div class="panel-heading">
        商品信息
    </div>
    <div class="panel-body table-responsive">
        <table class="table table-hover">
            <thead class="navbar-inner">
            <tr>
                <th class="col-md-5 col-lg-1">ID</th>
                <th class="col-md-5 col-lg-3">商品标题</th>
                <th class="col-md-5 col-lg-3">商品规格</th>
                <th class="col-md-5 col-lg-3">均摊支付金额</th>
                <th class="col-md-5 col-lg-3">现价/原价/成本价</th>
                <th class="col-md-5 col-lg-1">购买数量</th>
                {{--<th class="col-md-5 col-lg-1" style="color:red;">折扣前<br/>折扣后</th>--}}
                <th class="col-md-5 col-lg-1">操作</th>
            </tr>
            </thead>
            @foreach ($order['has_many_order_goods'] as $order_goods)

                <tr>
                    <td>{{$order_goods['goods_id']}}</td>
                    <td>
                        <a href="{{yzWebUrl($edit_goods, array('id' => $order_goods['goods_id']))}}">{{$order_goods['title']}}</a>
                    </td>
                    <td>{{$order_goods['goods_option_title']}}</td>
                    <td>{{$order_goods['payment_amount']}}</td>
                    <td>{{$order_goods['goods_price']}}
                        /{{$order_goods['goods_market_price']}}
                        /{{$order_goods['goods_cost_price']}}元
                    </td>
                    <td>{{$order_goods['total']}}</td>
                    {{--<td style='color:red;font-weight:bold;'>{{sprintf('%.2f', $order_goods['goods_price']/$order_goods['total'])}}--}}
                        {{--<br/>{{sprintf('%.2f', $order_goods['payment_amount']/$order_goods['total'])}}--}}
                    {{--</td>--}}
                    <td>
                        <a href="{!! yzWebUrl($edit_goods, array('id' => $order_goods['goods']['id'])) !!}"
                           class="btn btn-default btn-sm" title="编辑"><i
                                    class="fa fa-edit"></i></a>&nbsp;&nbsp;
                    </td>
                </tr>
                <tr style="text-align: right;padding: 6px 0;border-top:none;">
                    <td colspan="8">
                        @if ($order_goods['goods']['status'] == 1)
                            <label data="1"
                                   class="label label-default  label-info">上架</label>
                        @else
                            <label data="1"
                                   class="label label-default label-info ">下架</label>
                        @endif
                        <label data="1"
                               class="label label-default label-info">
                            @if ($order_goods['goods']['type'] == 1)
                                实体商品
                            @else
                                虚拟商品
                            @endif
                        </label>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2">
                    @include('order.modals')
                    @include($ops)
                </td>
                <td colspan="8">
                </td>
            </tr>
        </table>
    </div>
</div>
<script></script>