@extends('layouts.base')
@section('title', '会员统计')
@section('content')

    <div class="rightlist">

        @include('layouts.tabs')

        <div class='panel panel-default form-horizontal form'>
            <div class='panel-body'>
                <div id="todayTrends" style="width: 95%;height:300px;float: left;"></div>
            </div>
        </div>

        <div class='panel panel-default form-horizontal form'>
            <div class='panel-heading'>订单动态明细</div>
            <div class='panel-body'>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        <span style="font-size: 22px;">订单相关</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>下单总数</th>
                                <th>下单总金额</th>
                                <th>支付总数</th>
                                <th>支付总金额</th>
                                <th>支付转化率</th>
                                <th>运费总额</th>
                            </tr>
                            </thead>
                            <tbody>



                                <tr>
                                    <th>122</th>
                                    <th>121</th>
                                    <th>31</th>
                                    <th>1231</th>
                                    <th>123</th>
                                    <th>123</th>
                                </tr>
                        </table>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        <span style="font-size: 22px;">支付方式</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>余额支付</th>
                                <th>微信支付</th>
                                <th>支付宝支付</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th>34535</th>
                                <th>5345</th>
                                <th>3453</th>
                            </tr>
                        </table>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                        <span style="font-size: 22px;">订单折扣</span>
                    </label>
                    <div class="col-sm-9 col-xs-12">

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>积分抵扣</th>
                                <th>爱心值抵扣</th>
                                <th>会员折扣金额</th>
                                <th>优惠总金额</th>
                            </tr>
                            </thead>
                            <tbody>



                            <tr>
                                <th>3453</th>
                                <th>453</th>
                                <th>3453</th>
                                <th>345</th>
                            </tr>
                        </table>

                    </div>
                </div>

            </div>
        </div>


    </div>


    <script type="text/javascript">



        //会员性别统计
        var todayTrends = echarts.init(document.getElementById('todayTrends'));

        charts_data = {!! $charts_data !!};
        today_option = {
            //backgroundColor: '#394056',
            title: {
                text: '订单动态'
            },
            tooltip: {
                trigger: 'axis'
            },

            toolbox: {
                feature: {
                    saveAsImage: {show: true}
                }
            },
            legend: {
                data: ['今日下单数量', '今日支付订单', '今日完成订单']
            },
            xAxis: [{
                type: 'category',
                boundaryGap: true,
                axisLine: {
                    lineStyle: {
                        color: '#57617B'
                    }
                },
                data: ['0:00', '2:00', '4:00', '6:00', '8:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00']
            }],
            yAxis: [{
                type: 'value',
                //name: '数量',
                axisLine: {
                    show: false
                }

            }],
            series: [
                {
                    name: '今日下单数量',
                    type: 'line',
                    data: charts_data.created_order
                }, {
                    name: '今日支付订单',
                    type: 'line',
                    data: charts_data.pay_order
                },
                {
                    name: '今日完成订单',
                    type: 'line',
                    data: charts_data.received_order
                }]
        };

        todayTrends.setOption(today_option);






    </script>




@endsection

