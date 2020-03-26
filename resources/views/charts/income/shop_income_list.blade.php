@extends('layouts.base')

@section('content')
@section('title', trans('订单收益统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>--}}

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class='alert alert-info'>
                    <p>1、未分润金额：按照分润插件设置条件计算的分润金额 -订单实际分润金额</p>
                    <p>2、商城收益：分成平台订单，供应商订单，门店订单和收银台订单</p>
                    <p>平台&供应商订单：商城收益=订单实付金额-商品成本价</p>
                    <p>门店&收银台订单：商城收益=订单实付金额*平台提成比例</p>
                </div>
                <div class="card">
                    <div class="card-content">
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-2">
                                <input type="text" class="form-control"  name="search[member_id]" value="{{$search['member']?$search['member']:''}}" placeholder="会员ID"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <input type="text" class="form-control"  name="search[order_sn]" value="{{$search['member']?$search['member']:''}}" placeholder="订单号查询"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="会员昵称/姓名/手机"/>
                            </div>
                            <div class='form-group col-xs-12 col-sm-6'>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="search[is_time]" value="1"
                                               @if($search['is_time'] == '1')checked="checked"@endif>
                                    </span>
                                    {!!app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                                                            'starttime'=>$search['time']['start'] ?: date('Y-m-d H:i:s'),
                                                                            'endtime'=>$search['time']['end'] ?: date('Y-m-d H:i:s'),
                                                                            'start'=>0,
                                                                            'end'=>0
                                                                            ], true)!!}
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                                <button type="submit" name="export" value="1" id="export" class="btn btn-default">导出 Excel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th class="col-md-4 text-center" style="white-space: pre-wrap;">订单号</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">购买者</th>
                            <th class="col-md-2 text-center">订单金额</th>
                            <th class="col-md-4 text-center">订单类型</th>
                            <th class="col-md-4 text-center">商家</th>
                            <th class="col-md-2 text-center">未被分润</th>
                            <th class="col-md-2 text-center">商城收益</th>
                            <th class="col-md-2 text-center">供应商收益</th>
                            <th class="col-md-2 text-center">门店收益</th>
                            <th class="col-md-2 text-center">收银台收益</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $row)
                            <tr style="height: 40px; text-align: center">
                                <td>{{ $row['order_sn'] }}</td>
                                <td>
                                    @if(empty($row['buy_name']))
                                        未更新
                                    @else
                                        {{ $row['buy_name'] }}
                                    @endif
                                </td>
                                <td>{{ $row['price'] }}</td>
                                <td>
                                    @if($row->plugin_id == 1)供应商
                                    @elseif($row->plugin_id == 32)门店
                                    @elseif($row->plugin_id == 31)收银台
                                    @else商城
                                    @endif
                                </td>
                                <td>{{ $row['shop_name'] }}</td>
                                <td>{{ $row['undividend'] ?: '0.00' }}</td>
                                @if($row['plugin_id'] == 32)
                                    <td>{{ \Yunshop\StoreCashier\common\models\StoreOrder::where('order_id',$row->order_id)->value('fee') ?: '0.00' }}</td>
                                @elseif ($row['plugin_id'] == 31)
                                    <td>{{ \Yunshop\StoreCashier\common\models\CashierOrder::where('order_id',$row->order_id)->value('fee') ?: '0.00' }}</td>
                                @else
                                    <td>{{ sprintf("%01.2f",($row->price - $row->cost_price) > 0 ? $row->price - $row->cost_price : '0.00') }}</td>
                                @endif

                                <td>{{ $row->supplier ?: '0.00' }}</td>
                                <td>{{ $row->store ?: '0.00' }}</td>
                                <td>{{ $row->cashier ?: '0.00' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @include('order.modals')
            <div id="pager">{!! $pager !!}</div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('charts.income.shop-income-list.export') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection
