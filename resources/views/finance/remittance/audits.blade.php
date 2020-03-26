@extends('layouts.base')
@section('title', '转账审核列表')
@section('content')
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">转账审核</a></li>
        </ul>
    </div>
    <div id="app-remittance-audits" xmlns:v-bind="http://www.w3.org/1999/xhtml">
        <div style="float: right">

            <el-select v-model="searchParams.status_id" size="small" clearable placeholder="全部"
                       style="width: 120px" @change="search" v-loading="loading">
                <el-option
                        v-for="v in allStatus"
                        :key="v.id"
                        :label="v.name"
                        :value="v.id">
                </el-option>
            </el-select>
        </div>
        <el-table
                :data="data.data"
                style="width: 100%"
                :row-class-name="tableRowClassName">
            <el-table-column
                    align="center"
                    prop="id"
                    label="id"
                    width="100">
            </el-table-column>
            <el-table-column
                    align="center"
                    label="支付单号">
                <template slot-scope="scope">
                    <a v-bind:href="'{{ yzWebUrl('orderPay.detail', array('order_pay_id' => '')) }}'+[[scope.row.remittance_record.order_pay.id]]"
                       target="_blank">[[scope.row.remittance_record.order_pay.pay_sn]]</a>
                </template>
            </el-table-column>

            <el-table-column
                    align="center"
                    prop="remittance_record.order_pay.amount"
                    label="金额">
            </el-table-column>
            <el-table-column
                    align="center"
                    prop="member.nickname"
                    label="用户">
                <template slot-scope="scope">

                    <a v-bind:href="'{{ yzWebUrl('member.member.detail', array('id' => '')) }}'+[[scope.row.remittance_record.order_pay.uid]]"
                       target="_blank"><img v-bind:src="scope.row.member.avatar_image"
                                            style='width:30px;height:30px;padding:1px;border:1px solid #ccc'><br/>[[scope.row.member.nickname]]</a>
                </template>
            </el-table-column>

            <el-table-column
                    align="center"
                    prop="status_name"
                    label="状态">
            </el-table-column>

            <el-table-column
                    align="center"
                    prop="created_at"
                    label="创建时间">
            </el-table-column>
            <el-table-column
                    align="center"
                    fixed="right"
                    label="操作"
                    width="100">
                <template slot-scope="scope">
                    <a v-bind:href="'{{ yzWebUrl('remittanceAudit.detail', array('id' => '')) }}'+[[scope.row.id]]"
                       target="_blank">查看</a>

                </template>
            </el-table-column>
        </el-table>
        <div style="float: right">
            <el-pagination
                    background
                    layout="prev, pager, next"
                    :total="data.total"
                    :page-size="data.pagesize"
                    @current-change="handleCurrentChange"
                    v-loading="pageLoading">
            </el-pagination>
        </div>

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
            el: '#app-remittance-audits',
            delimiters: ['[[', ']]'],

            data() {
                let data =eval({!! $data !!});
                return {
                    data: data.remittanceAudits,

                    allStatus: [
                        {id: null, name: "全部"},
                        ...data.allStatus

                    ],

                    searchParams: {
                        ...data.searchParams,
                        "keywords": "",
                        "status_id": ""
                    },
                    loading: false,
                    pageLoading: false
                }
            },
            mounted: function () {
            },
            methods: {

                tableRowClassName({row, rowIndex}) {
                    if (row.state == 'completed') {
                        return 'success-row';
                    } else if (row.state == 'closed') {
                        return 'warning-row';

                    }
                    return '';
                },
                search() {
                    this.loading = true;

                    this.$http.post("{!! yzWebUrl('finance.remittance-audit.ajax')!!}", {...this.searchParams}).then(response => {
                        this.data = response.data.data.remittanceAudits;
                        this.loading = false;
                    }, response => {
                        console.log(response);
                        this.loading = false;
                    });
                },
                handleCurrentChange(val) {
                    this.pageLoading = true;

                    //this.$Loading.start();
                    this.$http.post("{!! yzWebUrl('finance.remittance-audit.ajax')!!}", {
                        ...this.searchParams,
                        page: val,
                        pagesize: this.data.pagesize
                    }).then(response => {
                        console.log(response);
                        //this.$Loading.finish();
                        this.pageLoading = false;

                        this.data = response.data.data.remittanceAudits;
                    }, response => {
                        this.pageLoading = false;

                        //this.$Loading.error();
                        console.log(response);
                    });
                }
            }
        });
    </script>
@endsection('js')
