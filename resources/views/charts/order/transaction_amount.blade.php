@extends('layouts.base')

@section('content')
@section('title', trans('交易额统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<style>
    .status-title{ color: rgb(140, 140, 140); padding-top: 20px;}
    .status-content{ margin: 20px 0;}
    .panel-heading{ border: 0 !important; font-size: 1.2em !important;}
    .panel-body-change{  font-size: 1.5em !important;}
</style>
<div class="w1200 m0a">
    <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="card">
                    <div class="card-header card-header-icon" data-background-color="rose">
                        <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title">交易额统计</h4>
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
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
            <table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                <tr class='trhead'>
                    <td colspan='8' style="text-align: left;">
                        总交易额统计&nbsp;&nbsp;&nbsp;商城: <span id="total">{{ $totalOrder['shop'] }}元</span>&nbsp;&nbsp;&nbsp;供应商: <span id="total">{{ $totalOrder['supplier'] }}元</span>&nbsp;&nbsp;&nbsp;门店: <span id="total">{{ $totalOrder['store'] }}元</span>&nbsp;&nbsp;&nbsp;收银台: <span id="total">{{ $totalOrder['cashier'] }}元</span>&nbsp;&nbsp;&nbsp;总汇总: <span id="total">{{ $totalOrder['cashier'] + $totalOrder['shop'] + $totalOrder['supplier'] + $totalOrder['store'] }}元</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row">
            <div class="col-md-12 text-center status-content">
                <h4 class="col-md-2 status-title">待支付订单</h4>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading ">商城</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitPayOrder['shop'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">供应商</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitPayOrder['supplier'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">门店</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitPayOrder['store'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">收银台</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitPayOrder['cashier'] ?: '0.00' }}元
                    </div>
                </div>
            </div>

            <div class="col-md-12 text-center status-content">
                <h4 class="col-md-2 status-title">待发货订单</h4>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">商城</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitSendOrder['shop'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">供应商</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitSendOrder['supplier'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">门店</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitSendOrder['store'] ?: '0.00' }}元
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center status-content">
                <h4 class="col-md-2 status-title">待收货订单</h4>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">商城</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitReceiveOrder['shop'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">供应商</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitReceiveOrder['supplier'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">门店</div>
                    <div class="panel-body panel-body-change">
                        {{ $waitReceiveOrder['store'] ?: '0.00' }}元
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center status-content">
                <h4 class="col-md-2 status-title">已完成订单</h4>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">商城</div>
                    <div class="panel-body panel-body-change">
                        {{ $completedOrder['shop'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">供应商</div>
                    <div class="panel-body panel-body-change">
                        {{ $completedOrder['supplier'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">门店</div>
                    <div class="panel-body panel-body-change">
                        {{ $completedOrder['store'] ?: '0.00' }}元
                    </div>
                </div>
                <div class="panel panel-default col-md-2">
                    <div class="panel-heading">收银台</div>
                    <div class="panel-body panel-body-change">
                        {{ $completedOrder['cashier'] ?: '0.00' }}元
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('charts.order.transaction-amount.export') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection
