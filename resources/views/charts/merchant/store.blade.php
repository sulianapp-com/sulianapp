@extends('layouts.base')
@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist" id="member-blade">
            @include('layouts.tabs')
            @if(app('plugins')->isEnabled('store-cashier'))
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class='alert alert-info'>
                        <p>1.按照供应商/门店/收银台完成订单金额进行排序；</p>
                        <p>2.未提现收入：供应商/门店/收银台可提现金额</p>
                        <p>3.提现中收入：处于待审核、待打款、打款中的提现记录</p>
                        <p>4.已打款收入：已成功提现打款到账的收入</p>
                    </div>
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">

                        <div class="form-group col-sm-6 col-lg-6 col-xs-12">
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
                                                                        ])!!}
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <button class="btn btn-success" type="submit" id="search"><i class="fa fa-search"></i> 搜索</button>
                            <button type="submit" name="export" value="1" id="export" class="btn btn-default">导出 Excel</button>
                        </div>
                    </form>
                </div>

                <div class="panel panel-default">
                    <table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                        <tr class='trhead'>
                            <td colspan='8' style="text-align: left;">
                                门店数量: <span id="total">{{ $storeTotal }}个</span>&nbsp;&nbsp;&nbsp;未提现收入: <span id="total">{{ $unWithdrawTotal }}元</span>&nbsp;&nbsp;&nbsp;提现中收入: <span id="total">{{ $withdrawingTotal }}元</span>&nbsp;&nbsp;&nbsp;已提现收入: <span id="total">{{ $withdrawTotal }}元</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class='panel-body'>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style='width:80px;'>排行</th>
                            <th>门店</th>
                            <th>交易完成总额</th>
                            <th>未提现收入</th>
                            <th>提现中收入</th>
                            <th>已提现收入</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($list as $key => $item)
                            <tr>
                                <td>
                                    @if($key <= 2)
                                        <label class='label label-danger' style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</label>
                                    @else
                                        <label class='label label-default'  style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</label>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($item['thumb_url']))
                                        <img src='{{ $item['thumb_url'] }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    @if(empty($item['name']))
                                        未更新
                                    @else
                                        {{ $item['name'] }}
                                    @endif
                                </td>
                                <td>{{ $item['price'] ?: '0.00' }}</td>
                                <td>{{ $item['un_withdraw'] ?: '0.00' }}</td>
                                <td>{{ $item['withdrawing'] ?: '0.00' }}</td>
                                <td>{{ $item['withdraw'] ?: '0.00' }} </td>
                            </tr>
                        @endforeach

                    </table>
                    {{--{!! $page !!}--}}
                </div>
            </div>
            @else
                未开启门店插件
            @endif

        </div>
    </div>
    <script>
        $(function () {
            $('#export').click(function () {
                $('#form1').attr('action', '{!! yzWebUrl('charts.merchant.store-income.export') !!}');
                $('#form1').submit();
            });
        });
    </script>
@endsection
