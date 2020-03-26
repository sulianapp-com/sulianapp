<script src="{{static_url('js/echarts.js')}}" type="text/javascript"></script>
<style>
    .main-panel>.content{background:#f5f5f5 !important;padding:0}
    body{background:#f5f5f5 !important}
    .all{background:#f5f5f5}
    .chart{background:#fff;margin:15px 0;border-radius:10px;}
    .plugin-box{padding:15px 0;width:calc(100% / 8);display:inline-block;text-align:center}
    .plugin-box-a{color:#000;}
    .plugin-icon{background:#29ba9b;width:80px;height:80px;color:#fff;text-align:center;border-radius:10px;display:inline-block;}
    .plugin-icon-i{}
    .plugin-name{line-height:36px;font-weight:800;font-size:16px;}
    .plugin-name-1{font-size:14px;}
    .plugin-title{padding-left:20px;}

    .total-box{padding:15px 0;width:calc(100% / 5 - 4px);display:inline-block;text-align:center;border-right:2px solid #ccc;}
    .total-box-noboder{border-right:0px solid #ccc}
    .total-box-text{font-size:22px;color:#29ba9b;font-weight:900}
    .total-box-text1{color:#ffd07c;}
    [v-cloak]{display:none}
</style>
@extends('layouts.base')

@section('content')
    <div class="all">
        <div id="app" v-cloak v-loading="loading">
            <div class="chart total">
                <div class="total-box">
                    <div>
                        <span class="total-box-text">￥</span>
                        <count-to class="total-box-text" :start-val='0' :end-val="today_order_money" :decimals='2' :duration=4000></count-to>
                    </div>
                    <div class="plugin-name plugin-name-1">
                        今日交易额
                    </div>
                </div>
                <div class="total-box">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="member_count" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                        会员总数
                    </div>
                </div>
                <div class="total-box">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="today_order_count" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                        今日订单数
                    </div>
                </div>
                <div class="total-box">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="to_be_paid" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                        待付款订单
                    </div>
                </div>
                <div class="total-box total-box-noboder">
                    <div>
                        <count-to class="total-box-text total-box-text1" :start-val='0' :end-val="to_be_shipped" :duration=4000></count-to>
                    </div>
                    <div  class="plugin-name plugin-name-1">
                        待发货订单
                    </div>
                </div>
            </div>
            <div class="chart">
                <div class="plugin-name plugin-title">订单趋势</div>
                <div ref="chartmain" style="width:100%; height: 400px;margin:0;padding:0;margin-top:20px;"></div>
            </div>
            <div class="chart plugin">
                <div class="plugin-name plugin-title">常用功能</div>
                <div v-for="(item,index) in plugins" :key="index" class="plugin-box">
                    <a :href="item.url" class="plugin-box-a">
                        <div class="plugin-icon">
                            <!-- <i class="fa" :class="item.icon" style="font-size:50px;margin-top:15px;"></i> -->
                            <img :src="item.icon_url" style="font-size:50px;width:80px;height:80px;">
                        </div>
                        <div class="plugin-name">
                            [[item.name]]
                        </div>
                    </a>
                </div>
            </div>
            <div class="chart">
                <div class="plugin-name plugin-title">销量排行</div>
                <el-table :data="list" style="width: calc(100% - 30px);margin-left:15px;padding-top:30px;" v-loading="loading">
                    <el-table-column prop="title" label="商品名称" ></el-table-column>
                    <el-table-column prop="real_sales" label="销量" align="center"> </el-table-column>
                    <el-table-column prop="created_at" label="创建时间" align="center"> </el-table-column>
                </el-table>
            </div>
            <div class="example-item">
            </div>
        </div>
    </div>

    <script>
        var app = new Vue({
            el:"#app",
            delimiters: ['[[', ']]'],
            name: 'test',
            data() {

                return{
                    chart_data:[],
                    plugins:[],
                    list:[{}],
                    today_order_money:0,//今日交易额
                    to_be_paid:0,//待付款订单
                    to_be_shipped:0,//待发货订单
                    today_order_count:0,//今日订单数
                    member_count:0,
                    loading:false,
                }
            },
            created() {


            },
            mounted() {
                let data = {!! $data?:'{goods:[],order:{}}' !!};
                this.setData(data);
            },
            methods: {
                setData(data) {
                    this.loading = true;

                    this.chart_data = data.chart_data;
                    this.plugins = data.plugins;
                    this.list = data.goods;
                    this.list.forEach((item,index) => {
                        item.title = this.escapeHTML(item.title)
                    });
                    this.today_order_money = Number(data.order.today_order_money);
                    this.to_be_paid = data.order.to_be_paid;
                    this.to_be_shipped = data.order.to_be_shipped;
                    this.today_order_count = data.order.today_order_count;
                    this.member_count = data.member_count;
                    this.getRef();
                    this.loading = false;

                },
                getData() {
                    this.loading = true;
                    this.$http.post('{!! yzWebFullUrl('survey.survey.survey') !!}').then(function (response) {
                            if (response.data.result){
                                this.setData(response.data)
                            }
                            else {
                                this.$message({message: response.data.msg,type: 'error'});
                            }
                            this.loading = false;
                        },function (response) {
                            this.$message({message: response.data.msg,type: 'error'});
                            this.loading = false;
                        }
                    );
                },
                getRef() {
                    //指定图标的配置和数据
                    var option = {
                        title:{
                            text:''
                        },
                        tooltip:{},
                        legend:{
                            data:['总订单','已完成','已发货']
                        },
                        xAxis:{
                            data:[this.chart_data[0].date,this.chart_data[1].date,this.chart_data[2].date,this.chart_data[3].date,this.chart_data[4].date,this.chart_data[5].date,this.chart_data[6].date]
                        },
                        yAxis:{

                        },
                        series:[
                            {name: ['总订单'],type:'line',data:[this.chart_data[0].total,this.chart_data[1].total,this.chart_data[2].total,this.chart_data[3].total,this.chart_data[4].total,this.chart_data[5].total,this.chart_data[6].total]},
                            {name: ['已完成'],type:'line',data:[this.chart_data[0].complete,this.chart_data[1].complete,this.chart_data[2].complete,this.chart_data[3].complete,this.chart_data[4].complete,this.chart_data[5].complete,this.chart_data[6].complete]},
                            {name: ['已发货'],type:'line',data:[this.chart_data[0].deliver_goods,this.chart_data[1].deliver_goods,this.chart_data[2].deliver_goods,this.chart_data[3].deliver_goods,this.chart_data[4].deliver_goods,this.chart_data[5].deliver_goods,this.chart_data[6].deliver_goods]},
                        ]
                    };
                    //初始化echarts实例
                    // var myChart = echarts.init(document.getElementById('chartmain'));
                    var myChart1 = echarts.init(this.$refs.chartmain);
                    //使用制定的配置项和数据显示图表
                    myChart1.setOption(option);
                },
                // 字符转义
                escapeHTML(a) {
                    a = "" + a;
                    return a.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, "\"").replace(/&apos;/g, "'");;
                },
            },
        })

    </script>
@endsection

