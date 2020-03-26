@extends('layouts.base')

@section('content')
@section('title', trans('一二级团队统计'))


<div class="w1200 m0a">
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="card">
                    <div class="card-header card-header-icon" data-background-color="rose">
                        <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title">一二级团队统计(只统计功能更新后的订单)</h4>
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-1">
                                <input class="form-control" name="search[member_id]" id="" type="text"
                                       value="{{$search['member_id']}}" placeholder="请输入会员ID">
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <input class="form-control" name="search[nickname]" id="" type="text"
                                       value="{{$search['nickname']}}" placeholder="会员昵称">
                            </div>
                            <div class="form-group col-xs-12 col-sm-1">
                                <input class="form-control" name="search[realname]" id="" type="text"
                                       value="{{$search['realname']}}" placeholder="会员姓名">
                            </div>
                            <div class="form-group col-xs-12 col-sm-2">
                                <input class="form-control" name="search[mobile]" id="" type="text"
                                       value="{{$search['mobile']}}" placeholder="会员手机">
                            </div>
                            <div class="form-group col-xs-12 col-sm-1">
                                <input class="form-control" name="search[year]" id="" type="text"
                                       value="{{$search['year']}}" placeholder="输入年：xxxx">
                            </div>
                            <div class="form-group col-xs-12 col-sm-1">
                                <select name='search[month]' class='form-control'>
                                    <option value='1' @if($search['month'] == '1') selected @endif>1月</option>
                                    <option value='2' @if($search['month'] == '2') selected @endif>2月</option>
                                    <option value='3' @if($search['month'] == '3') selected @endif>3月</option>
                                    <option value='4' @if($search['month'] == '4') selected @endif>4月</option>
                                    <option value='5' @if($search['month'] == '5') selected @endif>5月</option>
                                    <option value='6' @if($search['month'] == '6') selected @endif>6月</option>
                                    <option value='7' @if($search['month'] == '7') selected @endif>7月</option>
                                    <option value='8' @if($search['month'] == '8') selected @endif>8月</option>
                                    <option value='9' @if($search['month'] == '9') selected @endif>9月</option>
                                    <option value='10' @if($search['month'] == '10') selected @endif>10月</option>
                                    <option value='11' @if($search['month'] == '11') selected @endif>11月</option>
                                    <option value='12' @if($search['month'] == '12') selected @endif>12月</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                                <button  name="export"  id="export" class="btn btn-default">导出 Excel</button>
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
                            <th class="col-md-1 text-center" style="white-space: pre-wrap;">排行</th>
                            <th class="col-md-1 text-center">会员ID</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">会员</th>
                            <th class="col-md-3 text-center">姓名<br>手机号码</th>
                            <th class="col-md-2 text-center">一二级团队人数</th>
                            <th class="col-md-3 text-center">一二级团队订单总数</th>
                            <th class="col-md-3 text-center">一二级团队订单总额</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['data'] as $key => $row)
                            <tr style="height: 40px; text-align: center">
                                <td>
                                    @if($key <= 2)
                                        <labe class='label label-danger' style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                    @else
                                        <labe class='label label-default'  style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                    @endif
                                </td>
                                <td style="word-wrap:break-word; white-space: pre-wrap">{{$row['member_id']}}</td>
                                <td>
                                    <img src="{{tomedia($row['avatar'])}}"
                                         style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                    </br>
                                    {{$row['nickname']}}
                                </td>
                                <td>
                                    {{$row['realname']}}
                                    </br>
                                    {{$row['mobile']}}
                                </td>
                                <td>{{$row['level_num'] ?: '0'}}</td>
                                <td>{{$row['order_all'] ?: '0'}}</td>
                                <td>{{$row['price_all'] ?: '0.00'}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
       {{--     <div id="pager">{!! $pager !!}</div>--}}
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#search').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('charts.team.list') !!}');
            $('#form1').submit();
        });
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('charts.team.list.export') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection
