@extends('layouts.base')
@section('title', '队列设置')
@section('content')
    <style>

    </style>
    @include('layouts.tabs')
    <div id="app-vue">
        <template>
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active">队列设置</li>
                </ul>
            </div>
            <div class="rightlist">
                <div id="test-vue">
                    <el-form ref="form" :rules="rules" :model="form" label-width="17%">
                        <el-form-item label="开启多队列分类">
                            <el-form-item prop="queue.is_classify">
                                <el-radio v-model="form.queue.is_classify" :label="0">关闭</el-radio>
                                <el-radio v-model="form.queue.is_classify" :label="1">开启</el-radio>
                                <p class="help-block">开启多队列分类需要配置supervisor相应的类名</p>
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
                    queue: {
                        is_classify: '0',
                    },
                    ...temp,
                }
                let rules = {
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
                console.log(this.form.queue.is_classify,'2222')
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
                    this.$http.post("{!! yzWebUrl('supervisord.supervisord.queue') !!}", {'setting': this.form}).then(response => {
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
