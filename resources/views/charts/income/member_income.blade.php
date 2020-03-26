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
                        累计收入: <span id="total">{{ $total['total_amount'] }}元</span>&nbsp;&nbsp;&nbsp;未提现收入: <span id="total">{{ $total['unwithdraw'] }}元</span>&nbsp;&nbsp;&nbsp;已提现收入: <span id="total">{{ $total['withdraw'] }}元</span>&nbsp;&nbsp;&nbsp;扣除手续费: <span id="total">{{ $totalPoundage['total_poundage'] }}元</span><br>
                        分销佣金:{{$total['commission_dividend']}}元，经销商提成:{{$total['team_dividend']}}元，区域分红：{{$total['area_dividend']}}元，股东分红：{{$total['shareholder_dividend']}}元，招商分红：{{$total['merchant_dividend']}}元
                    </td>
                </tr>
            </table>
        </div>
        <div class="panel panel-default">
            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th class="col-md-2 text-center" style='width:80px;'>排行</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">会员</th>
                            <th class="col-md-2 text-center">累计收入</th>
                            <th class="col-md-2 text-center">未提现收入</th>
                            <th class="col-md-2 text-center">已提现收入</th>
                            <th class="col-md-2 text-center">扣除手续费</th>
                            <th class="col-md-2 text-center">分销佣金</th>
                            <th class="col-md-2 text-center">经销商提成</th>
                            <th class="col-md-2 text-center">股东分红</th>
                            <th class="col-md-2 text-center">区域分红</th>
                            <th class="col-md-2 text-center">招商分红</th>
                            <th class="col-md-2 text-center">收入明细</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $key => $row)

                            <tr style="height: 40px; text-align: center">
                                <td>
                                    @if($key <= 2)
                                        <labe class='label label-danger' style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                    @else
                                        <labe class='label label-default'  style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($row->hasOneMember->avatar))
                                        <img src='{{ $row->hasOneMember->avatar }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    @if(empty($row->hasOneMember->nickname))
                                        未更新
                                    @else
                                        {{ $row->hasOneMember->nickname }}
                                    @endif
                                </td>
                                <td>{{ $row['total_amount'] ?: '0.00' }}</td>
                                <td>{{ $row['unwithdraw'] ?: '0.00' }}</td>
                                <td>{{ $row['withdraw'] ?: '0.00' }}</td>
                                <td>{{ $row->hasOneWithdraw->total_poundage ?: '0.00' }}</td>
                                <td>{{ $row['commission_dividend'] ?: '0.00' }}</td>
                                <td>{{ $row['team_dividend'] ?: '0.00' }}</td>
                                <td>{{ $row['shareholder_dividend'] ?: '0.00' }}</td>
                                <td>{{ $row['area_dividend'] ?: '0.00' }}</td>
                                <td>{{ $row['merchant_dividend'] ?: '0.00' }}</td>
                                <td>
                                    <a href="{!!  yzWebFullUrl('charts.income.member-income.detail',['id' => $row['member_id']]) !!}" class="btn btn-primary">收入详情</a>
                                </td>
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
@endsection
