@extends('layouts.base')
@section('content')

<link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>

<div class="w1200 m0a">
    <div class="rightlist" id="member-blade">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="">七日内提现统计</a></li>
            </ul>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <form action="{{yzWebUrl('finance.withdraw-statistics.index')}}" method="post" class="form-horizontal" role="form" id="form1">

                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>

                                <div class='form-input'>
                                    <div class="search-select">
                                        {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', [
                                        'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                                        'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                                        'start'=>0,
                                        'end'=>0
                                        ], true) !!}
                                    </div>
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
                <table class="table table-hover" style="text-align: center;">
                    <thead>
                    <tr>
                        <th style="text-align: center; height: 60px;">提现日期</th>
                        <th style="text-align: center;">提现到余额</th>
                        <th style="text-align: center;">提现到微信</th>
                        <th style="text-align: center;">提现到支付宝</th>
                        <th style="text-align: center;">总计提现金额</th>

                    </tr>
                    </thead>
                    <tbody>


                    @foreach($data as $key => $item)
                    <tr>
                        <td style="height: 60px;">{{ $item['time'] }}</td>
                        <td style="height: 60px;">{{ $item['balance'] }}</td>
                        <td style="height: 60px;">{{ $item['wechat'] }}</td>
                        <td style="height: 60px;">{{ $item['alipay'] }}</td>
                        <td style="height: 60px;">{{ $item['balance'] + $item['wechat'] + $item['alipay'] }}</td>
                    </tr>

                    @endforeach


                </table>
            </div>
        </div>
    </div>
</div>
@endsection
