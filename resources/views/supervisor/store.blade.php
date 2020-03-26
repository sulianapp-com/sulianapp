@extends('layouts.base')
@section('title', '服务器设置')
@section('content')
    <style>

    </style>
    @include('layouts.tabs')
    <div id="app-vue">
        <template>
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">IP设置</li>
                </ul>
            </div>
            <div class="rightlist">
                <div id="test-vue">
                    <el-form ref="form" :rules="rules" :model="form" label-width="17%">

                        <el-form-item label="ip 地址">
                            <el-form-item prop="address.ip">
                                <el-input :placeholder="form.address.ip"
                                          v-model.String="form.address.ip"
                                          style="width: 27%">
                                </el-input>
                                <p class="help-block">为空默认：http://127.0.0.1</p>
                            </el-form-item>
                        </el-form-item>

                        <el-form-item>
                            <el-button type="success" @click.native.prevent="onSubmit" v-loading="formLoading">提交
                            </el-button>
                        </el-form-item>
                    </el-form>
                </div>


            </div>
        </template>
    </div>
    <script>
        var app = new Vue({
            el: '#app-vue',
            delimiters: ['[[', ']]'],
            data() {
                // 默认数据
                let temp = JSON.parse('{!! $setting?:'{}' !!}');
                console.log(temp);
                let temp1 = {
                        address: {
                            'ip': 'http://127.0.0.1',
                        },
                        ...temp,
                    }
                //验证规则
                // let amountRules = {
                //     type: 'number',
                //     min: 0,
                //     max: 999999999,
                //     message: '请输入正确金额',
                //     transform(value) {
                //         console.log(value);
                //         return Number(value)
                //     }
                // };
                let rules = {
                    // 'service.name': [],
                };

                return {
                    form: temp1,
                    props: {
                        label: 'areaname',
                        children: 'children',
                        isLeaf: 'isLeaf'
                    },
                    name:'11111',
                    loading: false,
                    formLoading: false,
                    centerDialogVisible: false,
                    treeData: [],
                    rules: rules
                }
            },
            mounted: function () {
                console.log(this.form.address.ip,'2222')
            },
            methods: {
                onSubmit() {
                    if (this.formLoading) {
                        return;
                    }
                    this.formLoading = true;

                    this.$refs.form.validate((valid) => {
                        console.log(valid)
                });
                    this.$http.post("{!! yzWebUrl('supervisord.supervisord.store') !!}", {'setting': this.form}).then(response => {
                        if (response.data.result) {
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

                    this.formLoading = false;
                }, response => {
                        console.log(response);
                    });
                },
                goBack() {
                    window.history.back();
                },
                checkAreas(node,checked,children) {
                    if(node.isLeaf){
                        return;
                    }
                    if(checked){

                    }
                },

            }
        });
    </script>
@endsection
