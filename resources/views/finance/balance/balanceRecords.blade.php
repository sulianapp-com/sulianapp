@extends('layouts.base')
@section('title', '余额明细')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">财务／余额明细</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site" />
                    <input type="hidden" name="a" value="entry" />
                    <input type="hidden" name="m" value="yun_shop" />
                    <input type="hidden" name="do" value="5201" />
                    <input type="hidden" name="route" value="finance.balance-records.index" id="route" />
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                        <div class="">
                            <input type="text" placeholder="会员ID" class="form-control"  name="search[member_id]" value="{{$search['member_id']}}"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" class="form-control"  name="search[realname]" value="{{$search['realname']}}" placeholder="昵称／姓名／手机号"/>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[member_level]' class='form-control'>
                                <option value=''>会员等级</option>

                                @foreach($memberLevels as $list)
                                    <option value='{{ $list['id'] }}' @if($search['member_level'] == $list['id']) selected @endif>{{ $list['level_name'] }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[member_group]' class='form-control'>
                                <option value=''>会员分组</option>
                                @foreach($memberGroups as $list)
                                    <option value='{{ $list['id'] }}' @if($search['member_group'] == $list['id']) selected @endif>{{ $list['group_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[source]' class='form-control'>
                                <option value=''>业务类型</option>
                                @foreach($sourceName as $key => $value)
                                    <option value='{{ $key }}' @if($search['source'] == $key) selected @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[type]' class='form-control'>
                                <option value=''>收入／支出</option>
                                <option value='1' @if($search['type']=='1') selected @endif>收入</option>
                                <option value='2' @if($search['type']=='2') selected @endif>支出</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <input type="text" placeholder="订单号" class="form-control"  name="search[order_sn]" value="{{$search['order_sn']}}"/>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">

                        <div class="time">

                            <select name='search[search_time]' class='form-control'>
                                <option value='0' @if($search['search_time']=='0') selected @endif>不搜索时间</option>
                                <option value='1' @if($search['search_time']=='1') selected @endif>搜索时间</option>
                            </select>
                        </div>
                        <div class="search-select">
                            {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                            'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                            'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                            'start'=>0,
                            'end'=>0
                            ], true) !!}
                        </div>
                    </div>

                    <div class="form-group  col-xs-12 col-sm-7 col-lg-4">
                        <div class="">
                            <button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出Excel</button>
                            <input type="hidden" name="token" value="{{$var['token']}}" />
                            <button class="btn btn-success "><i class="fa fa-search"></i>搜索</button>

                        </div>
                    </div>

                </form>
            </div>

            
        </div>
        <div class="clearfix">
            <div class="panel panel-default">
                <div class="panel-heading">总数：{{ $pageList->total() }}</div>
                <div class="panel-body">
                    <table class="table table-hover" style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:11%;text-align: center;'>时间</th>
                            <th style='width:8%;text-align: center;'>会员ID</th>
                            <th style='width:8%;text-align: center;'>粉丝</th>
                            <th style='width:12%;text-align: center'>姓名<br/>手机号码</th>
                            <th style='width:8%;text-align: center'>余额</th>
                            <th style='width:15%;text-align: center'>业务类型</th>
                            <th style='width:8%;text-align: center'>收入\支出</th>
                            <th style='width:8%;text-align: center'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center">
                                <td style="text-align: center;">{{ $list->created_at }}</td>
                                <td style="text-align: center;">{{ $list->member_id }}</td>
                                <td style="text-align: center;">
                                    @if($list->member->avatar || $shopSet['headimg'])
                                        <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg']) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    {{ $list->member->nickname ? $list->member->nickname : '未更新' }}
                                </td>
                                <td>{{ $list->member->realname }}<br/>{{ $list->member->mobile }}</td>
                                <td><label class="label label-danger">余额：{{ $list->new_money }}</label></td>
                                <td>{{ $list->service_type_name }}</td>
                                <td>{{ $list->change_money }}</td>
                                <td  style="overflow:visible;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('finance.balance.lookBalanceDetail', array('id' => $list->id )) }}" style="margin-bottom: 2px">查看详情</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $page !!}

                </div>
            </div>
        </div>
    </div>
        <script language='javascript'>
            $(function () {
                $('#export').click(function(){
                    $('#route').val("finance.balance-records.export");
                    $('#form1').submit();
                    $('#route').val("finance.balance-records.index");
                });
            });
        </script>

@endsection