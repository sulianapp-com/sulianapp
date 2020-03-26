@extends('layouts.base')
<script src="{{static_url('js/echarts.js')}}" type="text/javascript"></script>
<script src="{{static_url('js/china.js')}}" type="text/javascript"></script>
@section('content')
@section('title', trans('手机归属地统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

<div class="w1200 m0a">
    <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="card">
                    <div class="card-header card-header-icon" data-background-color="rose">
                        <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title">手机归属地统计</h4>
                    </div>
                    <div>
                        <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
                        <div id="main" style="width: 1000px;height:1000px; margin: auto"></div>
                            <script type="text/javascript">
                                function randomData() {
                                    return Math.round(Math.random()*500);
                                }
                                var provinceData = [
                                    {name: '天津',value: 0 },
                                    {name: '北京',value: 0 },
                                    {name: '上海',value: 0 },{name: '重庆',value: 0 },
                                    {name: '河北',value: 0 },{name: '河南',value: 0 },
                                    {name: '云南',value: 0 },{name: '辽宁',value: 0 },
                                    {name: '黑龙江',value: 0 },{name: '湖南',value: 0 },
                                    {name: '安徽',value: 0 },{name: '山东',value: 0 },
                                    {name: '新疆',value: 0 },{name: '江苏',value: 0 },
                                    {name: '浙江',value: 0 },{name: '江西',value: 0 },
                                    {name: '湖北',value: 0 },{name: '广西',value: 0 },
                                    {name: '甘肃',value: 0 },{name: '山西',value: 0 },
                                    {name: '内蒙古',value: 0 },{name: '陕西',value: 0 },
                                    {name: '吉林',value: 0 },{name: '福建',value: 0 },
                                    {name: '贵州',value: 0 },{name: '广东',value: 0 },
                                    {name: '青海',value: 0 },{name: '西藏',value: 0 },
                                    {name: '四川',value: 0 },{name: '宁夏',value: 0 },
                                    {name: '海南',value: 0 },{name: '台湾',value: 0 },
                                    {name: '香港',value: 0 },{name: '澳门',value: 0 },
                                ];
                                var myData = JSON.parse('{!! $phone_map_data !!}');
                                provinceData.map(function(value,index){
                                    var realData = myData.find(function(item){

                                        return item.province == value.name;
                                    });

                                    if(realData){
                                        provinceData[index] = {name:realData.province,value:realData.num};
                                    }
                                });

                                var optionMap = {
                                    backgroundColor: '#FFFFFF',
                                    title: {
                                        text: '全国地图大数据',
                                        subtext: '',
                                        x:'center'
                                    },
                                    tooltip : {
                                        trigger: 'item'
                                    },

                                    //左侧小导航图标
                                    visualMap: {
                                        show : true,
                                        x: 'left',
                                        y: 'center',
                                        splitList: [
                                            {start: 500},{start: 400, end: 500},
                                            {start: 300, end: 400},{start: 200, end: 300},
                                            {start: 100, end: 200},{start: 0, end: 100},
                                        ],
                                        color: ['#5475f5', '#9feaa5', '#85daef','#74e2ca', '#e6ac53', '#9fb5ea']
                                    },

                                    //配置属性
                                    series: [{
                                        name: '数据',
                                        type: 'map',
                                        mapType: 'china',
                                        roam: true,
                                        label: {
                                            normal: {
                                                show: true  //省份名称
                                            },
                                            emphasis: {
                                                show: false
                                            }
                                        },
                                        data:provinceData  //数据
                                    }]
                                };
                                //初始化echarts实例
                                var myChart = echarts.init(document.getElementById('main'));

                                //使用制定的配置项和数据显示图表
                                myChart.setOption(optionMap);

                            </script>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th><h5>省市</h5></th>
                            <th><h5>会员数量</h5></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($phone_data as $row)
                        <tr>
                            <td>{{ $row['province']?:未知 }}</td>
                            <td>{{ $row['num'] }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
@endsection
