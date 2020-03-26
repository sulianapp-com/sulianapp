@extends('layouts.base')

@section('content')
@section('title', trans('手续费统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>--}}

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="card">
                    <div class="card-content">
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
                        累计手续费: <span id="total">{{ $total['poundage'] }}元</span>&nbsp;&nbsp;&nbsp;累计劳务税: <span id="total">{{ $total['servicetax'] }}元</span>
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
                            <th class="col-md-2 text-center" style='width:80px;'>日期</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">手续费</th>
                            <th class="col-md-2 text-center">劳务税</th>
                            <th class="col-md-2 text-center">总计</th>
                            <th class="col-md-2 text-center">查看明细</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $key => $row)

                            <tr style="height: 40px; text-align: center">
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['poundage'] ?: '0.00' }}</td>
                                <td>{{ $row['servicetax'] ?: '0.00' }}</td>
                                <td>{{ sprintf("%01.2f",($row['poundage'] + $row['servicetax'])) ?: '0.00' }}</td>
                                <td>
                                    <a href="{!!  yzWebFullUrl('charts.income.poundage.detail',['date' => $row['date']]) !!}" class="btn btn-primary">收入详情</a>
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
<script>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('charts.income.poundage.export') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection
