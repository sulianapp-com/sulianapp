@extends('layouts.base')
@section('title', '充值详情')
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
                <a href="{{yzWebUrl('excelRecharge.records.index')}}">
                    <span>充值记录</span>
                </a>
                <span>>></span>
                <a href="#">
                    <span>充值详情</span>
                </a>
            </div>
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">详情总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:6%; text-align: center;'>主键ID</th>
                            <th style='width:12%; text-align: center;'>充值时间</th>
                            <th style='width:12%; text-align: center;'>充值会员</th>
                            <th style='width:12%; text-align: center;'>充值数量</th>
                            <th style='width:12%; text-align: center;'>充值状态</th>
                            <th style='width:30%; text-align: center;'>备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="height: 59px">
                                <td style="text-align: center;">{{ $list->id }}</td>
                                <td style="text-align: center;">{{ $list->created_at }}</td>
                                <td style="text-align: center;">
                                    @if($list->member->avatar)
                                        <img src='{{ $list->member->avatar ? yz_tomedia($list->member->avatar) : '' }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                                        <br/>
                                    @endif
                                    {{ $list->member->nickname ?: '未更新'}}
                                </td>
                                <td style="text-align: center;">{{ $list->amount }}</td>
                                <td style="text-align: center;">
                                    @if($list->status)
                                        <label class="label label-success">成功</label>
                                    @else
                                        <label class="label label-danger">失败</label>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a style="color: #0a0a0a" title="{{ $list->remark }}">
                                        <span>{{ $list->remark }}</span>
                                    </a>
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
