@extends('layouts.base')
@section('title', '订单支付异常记录')
@section('content')
    <div id="app-orders" xmlns:v-bind="http://www.w3.org/1999/xhtml">
        <template>
            <el-table
                    :data="list"
                    style="width: 100%"
                    >
                <el-table-column
                        prop="id"
                        label="id">
                </el-table-column>
                {{--<el-table-column--}}
                        {{--prop="uid"--}}
                        {{--label="用户id">--}}
                {{--</el-table-column>--}}
                <el-table-column
                        label="订单编号">
                    <template slot-scope="scope">
                        <a v-bind:href="'{{ yzWebUrl('order.detail', array('order_id' => '')) }}'+[[scope.row.id]]"
                           target="_blank">
                            [[scope.row.order_sn]]
                        </a>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="amount"
                        label="支付金额">
                </el-table-column>

                <el-table-column
                        prop="status_name"
                        label="状态">
                </el-table-column>
                <el-table-column
                        prop="pay_type_name"
                        label="支付方式">
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="创建时间">
                </el-table-column>
                <el-table-column
                        prop="pay_time"
                        label="支付时间">
                </el-table-column>
                <el-table-column
                        prop="refund_time"
                        label="退款时间">
                </el-table-column>
            </el-table>
        </template>

    </div>
    <style>
        .el-table .warning-row {
            background: oldlace;
        }

        .el-table .success-row {
            background: #f0f9eb;
        }
    </style>
@endsection('content')
@section('js')
    <script>
        var app = new Vue({
            el: '#app-orders',
            delimiters: ['[[', ']]'],

            data() {
                let orders = JSON.parse('{!! $orders !!}');

                return {
                    list: orders
                }
            },
            mounted: function () {
            },
            methods: {

            }
        });
    </script>
@endsection('js')
