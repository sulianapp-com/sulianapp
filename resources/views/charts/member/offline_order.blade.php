@extends('layouts.base')
@section('content')
<link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    <div class="rightlist" id="member-blade">
        @include('layouts.tabs')
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">

                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>

                                <div class='form-input'>
                                    <p class="input-group-addon" >会员ID</p>
                                    <input class="form-control price" style="width: 40%;" type="text" name="search[member_id]" value="{{ $search['member_id'] or ''}}">
                                </div>

                                <div class='form-input'>
                                    <p class="input-group-addon" >会员信息</p>
                                    <input class="form-control price" style="width: 40%;" type="text" name="search[member_info]" value="{{ $search['member_info'] or ''}}">
                                </div>

                                <div class=''>
                                    <p class="" align="center">注：每天凌晨1点执行数据统计，统计截止到前一天的数据；建议不要再同一时间设置数据自动备份、快照等计划任务！</p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                        <div class="">
                            <input type="submit" class="btn btn-block btn-success" value="搜索">
                        </div>

                    </div>
                </form>
            </div>

            <div class='panel-body'>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th style='width:80px;'>排行</th>
                        <th>会员</th>
                        <th>昵称</th>
                        <th>一级下线订单总数</th>
                        <th>一级下线订单总额</th>
                        <th>二级下线订单总数</th>
                        <th>二级下线订单总额</th>
                        <th>三级下线订单总数</th>
                        <th>三级下线订单总额</th>
                        <th>团队订单总数</th>
                        <th>团队订单总额</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($list as $key => $item)
                        <tr>
                            <td>
                                @if($key <= 2)
                                    <labe class='label label-danger' style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                @else
                                    <labe class='label label-default'  style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                @endif
                            </td>
                            <td>{{ $item->uid?:$item->member_id }}</td>
                            <td>
                                @if(!empty($item->belongsToMember->avatar))
                                    <img src='{{ $item->belongsToMember->avatar }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                @endif
                                @if(empty($item->belongsToMember->nickname))
                                    未更新
                                @else
                                    {{ $item->belongsToMember->nickname }}
                                @endif
                            </td>
                            <td>{{ $item->first_order_quantity }}</td>
                            <td>{{ number_format(($item->first_order_amount), 2) }}</td>
                            <td>{{ $item->second_order_quantity }}</td>
                            <td>{{ number_format(($item->second_order_amount), 2) }}</td>
                            <td>{{ $item->third_order_quantity }}</td>
                            <td>{{ number_format(($item->third_order_amount), 2) }}</td>
                            <td>{{ $item->team_order_quantity }}</td>
                            <td>{{ number_format(($item->team_order_amount), 2) }}</td>
                        </tr>
                    @endforeach

                </table>
                {!! $page !!}
            </div>
        </div>
    </div>
</div>
@endsection
