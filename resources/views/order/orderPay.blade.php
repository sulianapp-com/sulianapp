@extends('layouts.base')
@section('title', '订单支付记录')
@section('content')
    <div id="app-order-pay" xmlns:v-bind="http://www.w3.org/1999/xhtml">
        <template>
            <el-table
                    :data="list"
                    style="width: 100%"
                    :row-class-name="tableRowClassName">
                <el-table-column
                        prop="id"
                        label="id">
                </el-table-column>
                {{--<el-table-column--}}
                        {{--prop="uid"--}}
                        {{--label="用户id">--}}
                {{--</el-table-column>--}}
                <el-table-column
                        label="支付单号">
                    <template slot-scope="scope">
                        <a v-bind:href="'{{ yzWebUrl('orderPay.detail', array('order_pay_id' => '')) }}'+[[scope.row.id]]"
                           target="_blank">
                            [[scope.row.pay_sn]]
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
            el: '#app-order-pay',
            delimiters: ['[[', ']]'],

            data() {
                let orderPays = JSON.parse('{!! $orderPays !!}');

                return {
                    list: orderPays
                }
            },
            mounted: function () {
            },
            methods: {
                tableRowClassName({row, rowIndex}) {
                    if (row.status == 1) {
                        return 'success-row';
                    }else if(row.status == 2){
                        return 'warning-row';

                    }
                    return '';
                }
            }
        });
    </script>
@endsection('js')
