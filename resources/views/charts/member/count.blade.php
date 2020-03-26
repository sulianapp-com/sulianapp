@extends('layouts.base')
<script src="{{static_url('js/echarts.js')}}" type="text/javascript"></script>
@section('title', '会员统计')
@section('content')
    <div class="rightlist">

        @include('layouts.tabs')

        <div class='panel panel-default form-horizontal form'>
            <div class='panel-body'>
                <div id="sourceChart" style="width: 50%;height:300px;float: left;"></div>
                <div id="genderChart" style="width: 50%;height:300px;float: left;"></div>
            </div>
        </div>

        <div class='panel panel-default form-horizontal form'>
            <div class='panel-heading'>会员统计</div>
            <div class='panel-body'>


                @foreach($member_count as $key => $item)
                <div class="form-group">
                    <div class="col-sm-8 col-lg-12 col-xs-12">
                        <table class="table table-hover" >
                            <thead>
                            <tr style="text-align: center;">
                                <th style='width:150px;text-align: center;'>{{ $item['first_name'] }}</th>
                                <th style='width:150px;text-align: center;'>{{ $item['second_name'] }}</th>
                                <th>{{ $item['third_name'] }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr style="text-align: center;">
                                <td>{{ $item['first_value'] }}</td>
                                <td>{{ $item['second_value'] }}</td>
                                <td>
                                    <div class="progress" style="height: 20px; margin-top: 15px;">
                                        <div style="width: {{ $item['third_value'] }}; height: 20px; background: #00bf00;" class="progress-bar progress-bar-info"><span class='num'>{{ $item['third_value'] }}</span></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

            </div>
        </div>


    </div>


    <script type="text/javascript">



        //会员性别统计
        var genderChart = echarts.init(document.getElementById('genderChart'));

        gender_data = {!! $gender !!};
        gender_option = {
            title : {
                text: '性别比例',
                x: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['男','女','未知']
            },
            series : [
                {
                    name: '性别比例',
                    type: 'pie',
                    radius : '60%',
                    //center: ['50%', '60%'],
                    data:gender_data,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        genderChart.setOption(gender_option);


        //会员结构统计
        var sourceChart = echarts.init(document.getElementById('sourceChart'));

        source_data = {!! $source !!};
        gender_option = {
            title : {
                text: '会员结构统计',
                x: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['微信授权','绑定手机']
            },
            series : [
                {
                    name: '会员结构',
                    type: 'pie',
                    radius : ['40%', '60%'],
                    //center: ['50%', '60%'],
                    data:source_data,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        sourceChart.setOption(gender_option);





    </script>




@endsection

