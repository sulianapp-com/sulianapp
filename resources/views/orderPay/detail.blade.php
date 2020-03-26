@extends('layouts.base')
@section('title', '支付记录详情')
@section('content')

    <div id="app-order-pay" style="padding-top: 1%" xmlns:v-bind="http://www.w3.org/1999/xhtml">
        <el-form ref="form" :model="form" label-width="17%">

            <el-form-item label="支付单号">
                [[form.pay_sn]]
            </el-form-item>
            <el-form-item label="用户">
                <a v-bind:href="'{{ yzWebUrl('member.member.detail', array('id' => '')) }}'+[[form.uid]]"
                   target="_blank"><img v-bind:src="form.member.avatar_image"
                                        style='width:50px;height:50px;padding:1px;border:1px solid #ccc'><br/>[[form.member.nickname]]</a>
            </el-form-item>
            <el-form-item label="金额">
                [[form.amount]]
            </el-form-item>
            <el-form-item label="支付方式">
                [[form.pay_type_name]]
            </el-form-item>
            <el-form-item label="支付状态">
                [[form.status_name]]
                <a target="_blank" v-bind:href="'{{yzWebUrl('orderPay.fix.refund', array('order_pay_id' => ''))}}'+[[form.id]]">原路退款</a>
            </el-form-item>

            <el-form-item label="支付流程">
                <el-table :data="form.process">
                    <el-table-column width="150" property="name" label="标题"></el-table-column>
                    <el-table-column width="100" property="updated_at" label="更新时间"></el-table-column>
                    <el-table-column width="100" property="status_name" label="状态"></el-table-column>
                </el-table>
            </el-form-item>



            <el-form-item label="支付订单">
                <el-table :data="form.orders">
                    <el-table-column width="200" label="订单编号">
                        <template slot-scope="scope">
                            <a v-bind:href="'{{ yzWebUrl('order.detail', array('id' => '')) }}'+[[scope.row.id]]"
                               target="_blank">[[scope.row.order_sn]]</a>
                        </template>
                    </el-table-column>
                    <el-table-column width="200" label="订单商品">
                        <template slot-scope="scope">
                            <div v-for="order_goods in scope.row.order_goods">
                                <a v-bind:href="'{{ yzWebUrl('goods.goods.edit', array('id' => '')) }}'+[[order_goods.goods_id]]"
                                   target="_blank">
                                    <img v-bind:src="order_goods.thumb"
                                                            style='width:30px;height:30px;padding:1px;border:1px solid #ccc'>
                                    [[order_goods.title]]
                                </a>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column width="80" property="price" label="金额"></el-table-column>
                    <el-table-column width="80" property="status_name" label="状态"></el-table-column>
                    <el-table-column width="200" label="操作">
                        <template slot-scope="scope">
                            <a target="_blank" v-bind:href="'{{yzWebUrl('order.fix.pay-fail', array('order_id' => ''))}}'+[[scope.row.id]]">修复支付状态</a>
                        </template>
                    </el-table-column>
                </el-table>
            </el-form-item>
            <el-form-item label="支付平台记录">
                <el-table :data="form.pay_order">
                    <el-table-column width="150" property="third_type" label="平台"></el-table-column>
                    <el-table-column width="150" property="trade_no" label="交易号"></el-table-column>
                    <el-table-column width="150" property="price" label="金额"></el-table-column>
                    <el-table-column width="100" property="status_name" label="状态"></el-table-column>
                    <el-table-column width="100" property="updated_at" label="更新时间"></el-table-column>
                </el-table>
            </el-form-item>
            <el-form-item label="创建时间">
                [[form.created_at]]
            </el-form-item>
            <el-form-item label="支付时间">
                [[form.pay_time]]
            </el-form-item>
            <el-form-item label="退款时间">
                [[form.refund_time]]
            </el-form-item>
        </el-form>


    </div>

@endsection('content')
@section('js')
    <script>
        var app = new Vue({
            el: '#app-order-pay',
            delimiters: ['[[', ']]'],
            data() {
                let orderPay = JSON.parse('{!! $orderPay !!}');

                return {
                    rules: {},
                    form: orderPay,

                }
            },
            mounted: function () {
            },
            methods: {}
        });
    </script>
@endsection('js')
