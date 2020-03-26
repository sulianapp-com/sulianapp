@extends('layouts.base')
@section('title', '收入明细')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">

        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site" />
                    <input type="hidden" name="a" value="entry" />
                    <input type="hidden" name="m" value="yun_shop" />
                    <input type="hidden" name="do" value="5201" />
                    <input type="hidden" name="route" value="income.income-records.index" id="route" />
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
                            <select name='search[class]' class='form-control'>
                                <option value=''>业务类型</option>
                                @foreach($income_type_comment as $key => $item)
                                    <option value='{{ $item['class'] }}' @if($search['class'] == $item['class']) selected @endif>{{ $item['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[status]' class='form-control'>
                                <option value=''>提现状态</option>
                                <option value='0' @if($search['status']=='0') selected @endif>未提现</option>
                                <option value='1' @if($search['status']=='1') selected @endif>已提现</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="">
                            <select name='search[pay_status]' class='form-control'>
                                <option value=''>打款状态</option>
                                <option value='-1' @if($search['pay_status']=='-1') selected @endif>无效</option>
                                <option value='0' @if($search['pay_status']=='0') selected @endif>未审核</option>
                                <option value='1' @if($search['pay_status']=='1') selected @endif>未打款</option>
                                <option value='2' @if($search['pay_status']=='2') selected @endif>已打款</option>
                                <option value='3' @if($search['pay_status']=='3') selected @endif>已驳回</option>
                            </select>
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
                            {{--<button type="button" name="export" value="1" id="export" class="btn btn-default excel back ">导出Excel</button>--}}
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
                            <th style='width:8%;text-align: center'>收入金额</th>
                            <th style='width:15%;text-align: center'>业务类型</th>
                            <th style='width:8%;text-align: center'>提现状态</th>
                            <th style='width:8%;text-align: center'>打款状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageList as $list)
                            <tr style="text-align: center; height: 59px;">
                                <td style="text-align: center;">{{ $list->created_at }}</td>
                                <td style="text-align: center;">{{ $list->member_id }}</td>
                                <td style="text-align: center;">
                                    @if($list->member->avatar || $shopSet['headimg'])
                                        <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg']) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    {{ $list->member->nickname ? $list->member->nickname : '未更新' }}
                                </td>
                                <td>{{ $list->member->realname }}<br/>{{ $list->member->mobile }}</td>
                                <td><label class="label label-success">收入：{{ $list->amount }}</label></td>
                                <td>{{ $list->type_name }}</td>
                                <td>{{ $list->status_name }}</td>
                                <td>{{ $list->pay_status_name }}</td>
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