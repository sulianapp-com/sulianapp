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
                                <input class="form-control" name="search[order_sn]" type="text" value="{{ $search['order_sn'] or ''}}" placeholder="充值单号">
                                <input class="form-control" name="search[realname]" type="text" value="{{ $search['realname'] or ''}}" placeholder="会员ID／会员姓名／昵称／手机号">
                                <div class='form-input'>
                                    <p class="input-group-addon price">充值区间</p>
                                    <input class="form-control price" name="search[min_value]" type="text" value="{{ $search['min_value'] or ''}}" placeholder="最小">
                                    <p class="line">—</p>
                                    <input class="form-control price" name="search[max_value]" type="text" value="{{ $search['max_value'] or ''}}" placeholder="最大">
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
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{ $memberList }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:15%; text-align: center;'>充值单号</th>
                            <th style='width:10%; text-align: center;'>粉丝</th>
                            <th style='width:10%; text-align: center;'>会员信息<br/>手机号</th>
                            <th style='width:12%; text-align: center;'>充值时间</th>
                            <th style='width:10%; text-align: center;'>充值方式</th>
                            <th style='width:10%; text-align: center;'>充值金额<br/>状态</th>
                            <th style='width:13%; text-align: center;'>备注信息</th>
                            <th style='width:10%; text-align: center;'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center;">
                                <td>{{ $list->order_sn }}</td>
                                <td>
                                    @if($list->member->avatar || $shopSet['headimg'])
                                        <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg'])}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                        <br/>
                                    @endif
                                    {{ $list->member->nickname ? $list->member->nickname : '未更新' }}
                                </td>
                                <td>
                                    {{ $list->member->realname }}
                                    <br/>
                                    {{ $list->member->mobile }}
                                </td>
                                <td>{{ $list->created_at }}</td>
                                <td>
                                    @if($list->type == 0)
                                        <span class='label label-default'>{{ $list->type_name }}</span>
                                    @elseif($list->type ==1)
                                        <span class='label label-success'>{{ $list->type_name }}</span>
                                    @elseif($list->type == 2)
                                        <span class='label label-warning'>{{ $list->type_name }}</span>
                                    @elseif($list->type == 9 || $list->type == 10)
                                        <span class='label label-info'>{{ $list->type_name }}</span>
                                    @else
                                        <span class='label label-primary'>{{ $list->type_name }}</span>
                                    @endif

                                </td>
                                <td>
                                    {{ $list->money }}
                                    <br/>
                                    @if($list->status == 1)
                                        <span class='label label-success'>充值成功</span>
                                    @elseif($list->status == '-1')
                                        <span class='label label-warning'>充值失败</span>
                                    @else
                                        <span class='label label-default'>申请中</span>
                                    @endif

                                </td>
                                <td><a style="color: #0a0a0a" title="{{ $list->remark }}">{{ $list->remark }}</a></td>
                                <td>
                                    <a class='btn btn-default' href="{{ yzWebUrl('member.member.detail', array('id' => $list->member_id)) }}" style="margin-bottom: 2px">用户信息</a>
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