@extends('layouts.base')
@section('title', '站点设置')
@section('content')
    <style>

    </style>
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>站点设置</a></li>
        </ul>
    </div>
    <div class="rightlist">
        @include('layouts.tabs')
        <div id="test-vue">
            <el-form ref="form" :model="form" label-width="17%">
                <el-form-item label="强制https">
                    <el-radio v-model.bool="form.https" :label=1>开启</el-radio>
                    <el-radio v-model.bool="form.https" :label=0>关闭</el-radio>
                </el-form-item>


                <el-form-item label="域名">
                    <el-input placeholder=""
                              v-model="form.host"
                              style="width: 27%">
                        <template v-if="form.https == 1" slot="prepend">https://</template>
                        <template v-if="form.https == 0" slot="prepend">http://</template>
                    </el-input>
                </el-form-item>


                <el-form-item>
                    <el-button type="success" @click.native.prevent="onSubmit" v-loading="formLoading">提交
                    </el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>


    </div>

    <script>
        var app = new Vue({
            el: '#test-vue',
            delimiters: ['[[', ']]'],
            data() {
                // 默认数据
                let temp = JSON.parse('{!! $setting !!}');
                if (!temp || temp.length === 0) {
                    temp = {
                        https: 0,
                        host: '',

                    }
                }

                return {
                    form: temp,
                    loading: false,
                    formLoading: false,
                    centerDialogVisible: false,
                }
            },
            mounted: function () {
                console.log(this.form)
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
                    this.$http.post("{!! yzWebUrl('siteSetting.store.index') !!}", {'setting': this.form}).then(response => {
                        //console.log(response.data);
                        // return;
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


            }
        });
    </script>
@endsection

