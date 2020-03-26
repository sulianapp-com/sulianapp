@extends('layouts.base')
@section('title', '充值记录')
@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="member-blade" class="rightlist">
        <div class="panel panel-info">
            <div class="panel-heading">
                <span>当前位置：</span>
                <a href="{{yzWebUrl('excelRecharge.page.index')}}">
                    <span>批量充值</span>
                </a>
                <span>>></span>
                <a href="#">
                    <span>充值记录</span>
                </a>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">记录总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>主键ID</th>
                            <th style='width:12%; text-align: center;'>充值时间</th>
                            <th style='width:12%; text-align: center;'>充值类型</th>
                            <th style='width:12%; text-align: center;'>充值数量</th>
                            <th style='width:12%; text-align: center;'>失败数量</th>
                            <th style='width:12%; text-align: center;'>充值总额</th>
                            <th style='width:12%; text-align: center;'>成功总额</th>
                            <th style='width:12%; text-align: center;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->id }}</td>
                                <td style="text-align: center;">{{ $list->created_at }}</td>
                                <td style="text-align: center;">{{ $list->sourceName }}</td>
                                <td style="text-align: center;">{{ $list->total }}</td>
                                <td style="text-align: center;">{{ $list->failure }}</td>
                                <td style="text-align: center;">{{ $list->amount }}</td>
                                <td style="text-align: center;">{{ $list->success }}</td>
                                <td style="overflow:visible; text-align: center;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('excelRecharge.detail.index', array('recharge_id' => $list->id)) }}" style="margin-bottom: 2px">详细记录</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>

@endsection
