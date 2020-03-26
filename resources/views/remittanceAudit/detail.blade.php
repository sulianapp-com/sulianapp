@extends('layouts.base')
@section('title', '转账记录详情')
@section('content')

    <div id="app-remittance-audit" style="padding-top: 1%" xmlns:v-bind="http://www.w3.org/1999/xhtml">

        <el-form ref="remittanceAudit" :model="remittanceAudit" label-width="17%">
            <el-form-item label="支付单号">
                <a v-bind:href="'{{ yzWebUrl('orderPay.detail', array('order_pay_id' => '')) }}'+[[remittanceAudit.remittance_record.order_pay.id]]"
                   target="_blank">[[remittanceAudit.remittance_record.order_pay.pay_sn]]</a>
            </el-form-item>
            <el-form-item label="用户">
                <a v-bind:href="'{{ yzWebUrl('member.member.detail', array('id' => '')) }}'+[[remittanceAudit.remittance_record.order_pay.uid]]"
                   target="_blank">
                    <img v-bind:src="remittanceAudit.member.avatar_image"
                         style='width:50px;height:50px;padding:1px;border:1px solid #ccc'>
                    <br/>
                    [[remittanceAudit.member.nickname]]
                </a>
            </el-form-item>
            <el-form-item label="审核状态">
                [[remittanceAudit.status_name]]
            </el-form-item>
            <el-form-item label="审核备注">
                [[remittanceAudit.note]]
            </el-form-item>
            <el-form-item label="金额">
                [[remittanceAudit.remittance_record.order_pay.amount]] 元
            </el-form-item>
            <el-form-item label="转账图片">
                <img v-bind:src="remittanceAudit.remittance_record.report_url">
            </el-form-item>
            <el-form-item label="申请备注">
                [[remittanceAudit.remittance_record.note]]
            </el-form-item>
            <el-form-item label="申请时间">
                [[remittanceAudit.created_at]]
            </el-form-item>
            <el-form-item v-show="remittanceAudit.state == 'processing'">
                <el-button type="primary" @click="dialogConfirmVisible = true">确认收款</el-button>
                <el-button @click="dialogRejectVisible = true">拒绝</el-button>
            </el-form-item>
        </el-form>

        <el-dialog v-loading="rejectLoading" title="拒绝申请" :visible.sync="dialogRejectVisible">
            <el-form :model="auditOperate">
                <el-form-item label="备注">
                    <el-input
                            type="textarea"
                            :autosize="{ minRows: 4, maxRows: 8}"
                            placeholder="请输入内容"
                            v-model="auditOperate.note">
                    </el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogRejectVisible = false">取 消</el-button>
                <el-button type="primary" @click="
                           rejectAudit();
                ">确 定
                </el-button>
            </div>
        </el-dialog>
        <el-dialog
                v-loading="confirmLoading"
                title="确认收款"
                :visible.sync="dialogConfirmVisible"
                width="30%">
            <span>确认已收到汇款</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialogConfirmVisible = false">取 消</el-button>
                <el-button type="primary"
                           @click="
                           confirmAudit()
                           ">确 定</el-button>
            </span>
        </el-dialog>


    </div>

@endsection('content')
@section('js')
    <script>
        let app = new Vue({
            el: '#app-remittance-audit',
            delimiters: ['[[', ']]'],
            data() {
                let remittanceAudit = JSON.parse('{!! $remittanceAudit !!}');

                return {
                    rules: {},
                    remittanceAudit: remittanceAudit,
                    dialogConfirmVisible: false,
                    dialogRejectVisible: false,
                    confirmLoading: false,
                    rejectLoading: false,

                    auditOperate: {
                        note: ''
                    }
                }
            },
            mounted: function () {
            },
            methods: {
                rejectAudit() {
                    this.rejectLoading=true;
                    this.$http.post("{!! yzWebUrl('remittanceAudit.operation.reject',['process_id'=>'']) !!}"+[[this.remittanceAudit.id]],this.auditOperate).then(response => {
                        if (response.data.result) {
                            this.remittanceAudit = response.data.data.remittanceAudit;
                            this.$message({
                                message: response.data.msg,
                                type: 'success'
                            });
                        } else {
                            this.$message({
                                message: response.data.msg,
                                type: 'error'
                            });
                        }
                        this.rejectLoading=false;
                        this.dialogRejectVisible = false;
                    }, response => {
                        this.rejectLoading=false;
                        this.$message.error('操作失败');

                        console.log(response);
                    });
                },
                confirmAudit() {
                    this.confirmLoading=true;
                    this.$http.post("{!! yzWebUrl('remittanceAudit.operation.pass',['process_id'=>'']) !!}"+[[this.remittanceAudit.id]]).then(response => {
                        if (response.data.result) {
                            this.remittanceAudit = response.data.data.remittanceAudit;

                            this.$message({
                                message: response.data.msg,
                                type: 'success'
                            });
                        } else {
                            this.$message({
                                message: response.data.msg,
                                type: 'error'
                            });
                        }
                        this.confirmLoading=false;
                        this.dialogConfirmVisible = false;
                        this.$message('操作成功');
                    }, response => {
                        this.confirmLoading=false;
                        this.$message.error('操作失败');
                        console.log(response);
                    });
                },
            }
        });
    </script>
@endsection('js')
