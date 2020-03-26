@extends('layouts.base')

@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="member-blade" class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->

        <div class="right-titpos">
            @include('layouts.tabs')
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">
                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>
                                <input class="form-control" name="search[queue_id]" type="text" value="{{ $search['queue_id'] or ''}}" placeholder="队列ID">
                                <input class="form-control" name="search[uid]" type="text" value="{{ $search['uid'] or ''}}" placeholder="会员ID">
                                <input class="form-control" name="search[member]" type="text" value="{{ $search['member'] or ''}}" placeholder="会员姓名／昵称／手机号">
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
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{ $list->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:8%; text-align: center;'>ID</th>
                            <th style='width:16%; text-align: center;'>充值时间</th>
                            <th style='width:16%; text-align: center;'>粉丝</th>
                            <th style='width:16%; text-align: center;'>订单编号</th>
                            <th style='width:8%; text-align: center;'>赠送总数量</th>
                            <th style='width:8%; text-align: center;'>已赠送数量</th>
                            <th style='width:8%; text-align: center;'>剩余数量</th>
                            <th style='width:8%; text-align: center;'>单次赠送数量</th>
                            <th style='width:8%; text-align: center;'>最后一次</br>赠送数量</th>
                            <th style='width:8%; text-align: center;'>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $item)
                            <tr style="text-align: center;">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <a href="{!! yzWebUrl('member.member.detail', ['id'=>$item->uid]) !!}"><img src="{{$item->member->avatar}}" style="width:30px;height:30px;padding:1px;border:1px solid #ccc"><BR>{{$item->member->nickname}}</a>
                                </td>
                                <td>
                                    <a target="_blank"
                                       href="{{yzWebUrl('order.detail',['id' => $item->order_id])}}">
                                        {{$item->order->order_sn}}
                                    </a>
                                </td>
                                <td>{{ $item->point_total }}</td>
                                <td>{{ $item->finish_point }}</td>
                                <td>{{ $item->surplus_point }}</td>
                                <td>{{ $item->once_unit }}</td>
                                <td>{{ $item->last_point }}</td>
                                <td>{{ $item->status_name }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $pager !!}

                </div>
            </div>
        </div>

@endsection