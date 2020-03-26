@extends('layouts.base')

@section('title','注册芸商城')

@section('content')
    <script src="{{resource_get('static/yunshop/js/industry.js', 1)}}"></script>
    <script type="text/javascript">
        function formcheck(event) {

            if ($(':input[name="upgrade[key]"]').val() == '' || $(':input[name="upgrade[secret]"]').val() == '') {
                if($(':input[name="upgrade[key]"]').val() == '')
                    Tip.focus(':input[name="upgrade[key]"]', 'Key 不能为空');
                else
                    Tip.focus(':input[name="upgrade[secret]"]', '密钥不能为空')
                return false;
            }
            return true
        }
    </script>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">注册商城</a></li>
                </ul>
            </div>
            <div class="form-group message-box" style="display: none">
                <div class="span4">
                    <div class="alert alert-block">
                        <a class="close" data-dismiss="alert">×</a>
                        <span id="message"></span>
                    </div>
                </div>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-default" style="width:996px;">
                <div class='panel-body'>

                    <div id="register">
                        <template>
                            <el-row v-show="page=='register'">
                                <el-button type="info" @click="redirect(1)" plain>免费版</el-button>
                                <el-button type="primary" @click="redirect(2)" plain>授权版</el-button>
                            </el-row><!--register end-->

                            <el-form ref="form" :model="form" label-width="100px" class="demo-ruleForm" v-show="page=='auth'">
                                <el-form-item label="key" prop="key">
                                    <el-input v-model="key" placeholder="请输入key" autocomplete="off"></el-input>
                                </el-form-item>

                                <el-form-item label="密钥" prop="secret">
                                    <el-input v-model="secret" placeholder="请输入密钥" autocomplete="off"></el-input>
                                </el-form-item>
                                <el-form-item>
                                <el-button type="primary" @click.native="tapclickPas" >重置密钥</el-button>
                                </el-form-item>
                                <el-form-item>
                                    {{--<el-button type="primary" @click="reg_shop('cancel')" v-loading="formLoading" v-if="btn == 0">取消商城</el-button>--}}
                                    <el-button type="primary" @click="reg_shop('create')" :disabled="formLoading" v-if="btn == 1">注册商城</el-button>
                                </el-form-item>
                            </el-form><!--auth end-->

                            <el-form ref="form" :model="form" :rules="rules" label-width="100px" class="demo-ruleForm" v-show="page=='free'">
                                <el-form-item label="公司名称" prop="name">
                                    <el-input v-model="form.name" placeholder="请输入公司名称" autocomplete="off"></el-input>
                                </el-form-item>
                                <el-form-item label="行业" prop="trades">
                                    <el-select v-model="form.trades" value-key="id" style="width:100%" placeholder="请选择行业">
                                        <el-option v-for="item in opt_trades.data"
                                                   :key="item.id"
                                                   :label="item.name"
                                                   :value="item.name">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="所在区域" required>
                                    <el-col :span="4">
                                        <el-form-item prop="province">
                                            <el-select v-model="form.province" value-key="id" placeholder="省" @change="change_province">
                                                <el-option v-for="item in opt_province"
                                                           :key="item.id"
                                                           :label="item.areaname"
                                                           :value="item">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col style="text-align: center" :span="1">-</el-col>
                                    <el-col :span="4">
                                        <el-form-item prop="city">
                                            <el-select v-model="form.city" value-key="id" placeholder="市" @change="change_city">
                                                <el-option v-for="item in opt_city"
                                                           :key="item.id"
                                                           :label="item.areaname"
                                                           :value="item">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col style="text-align: center" :span="1">-</el-col>
                                    <el-col :span="4">
                                        <el-form-item prop="area">
                                            <el-select v-model="form.area" value-key="id" placeholder="区">
                                                <el-option v-for="item in opt_area"
                                                           :key="item.id"
                                                           :label="item.areaname"
                                                           :value="item">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                </el-form-item>
                                <el-form-item label="详细地址" prop="address">
                                    <el-input v-model="form.address" placeholder="请输入详细地址" autocomplete="off"></el-input>
                                </el-form-item>
                                <el-form-item label="验证码" prop="captcha">
                                    <el-input v-model="form.captcha" style="width:150px" placeholder="请输入验证码"></el-input>
                                </el-form-item>
                                <el-form-item label="手机号" prop="mobile">
                                    <el-input placeholder="请输入手机号" v-model="form.mobile" style="width:200px" autocomplete="off"></el-input>
                                    <el-button type="info" @click="sendSms()" style="width:150px; margin-left: 50px" plain :disabled="isDisabled">[[captcha_text]]</el-button>
                                </el-form-item>

                                <el-form-item>
                                    <el-button type="primary" @click.native.prevent="onSubmit" :disabled="formLoading">提交</el-button>
                                </el-form-item>
                            </el-form><!--free end-->
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var app = new Vue({
            el: '#register',
            delimiters: ['[[', ']]'],
            data() {
                // 默认数据
                let redirectUrl = JSON.parse('{!! $url !!}');
                let page = JSON.parse('{!!  $page !!}');
                let province = JSON.parse('{!! $province !!}');
                let set = JSON.parse('{!! $set !!}');

                var validateMobile = (rule, value, callback) => {
                    if (!(/^1\d{10}$/.test(value))) {
                        callback(new Error('手机号格式不正确'));
                    } else {
                        callback();
                    }
                };

                return {
                    page: page.type,
                    redirectUrl:redirectUrl,
                    form: {
                        name: '',
                        trades: '',
                        province: '',
                        city: '',
                        area: '',
                        address: '',
                        mobile: '',
                        captcha: ''
                    },
                    key: set.key,
                    secret: set.secret,
                    btn: set.btn,
                    opt_trades: industry,
                    opt_province: province.data,
                    opt_city:'',
                    opt_area:'',
                    t: 60,
                    captcha_text: '获取验证码',
                    isDisabled: false,
                    formLoading: false,
                    rules: {
                        name: [
                            { required: true, message: '请输入公司名称', trigger: 'blur' }
                        ],
                        trades: [
                            { required: true, message: '请选择行业', trigger: 'change' }
                        ],
                        province: [
                            { required: true, message: '请选择省', trigger: 'change' }
                        ],
                        city: [
                            { required: true, message: '请选择市', trigger: 'change' }
                        ],
                        area: [
                            { required: true, message: '请选择区', trigger: 'change' }
                        ],
                        address: [
                            { required: true, message: '请输入详情地址', trigger: 'blur' }
                        ],
                        mobile: [
                            { required: true, message: '请输入手机号', trigger: 'blur' },
                            { validator: validateMobile, trigger: 'blur' }
                        ],
                        captcha: [
                            { required: true, message: '请输入验证码', trigger: 'blur' }
                        ]
                    }
                }
            },
            mounted: function () {
            },
            methods: {
                redirect:function (type) {
                    switch (type) {
                        case 1:
                            location.href = this.redirectUrl.free;
                            break;
                        case 2:
                            location.href = this.redirectUrl.auth;
                            break;
                    }
                },
                sendSms:function () {
                    let that = this;
                    let rTime = that.t;

                    if (!(/^1\d{10}$/.test(this.form.mobile))) {
                        this.$refs.form.validateField('mobile');
                        return false;
                    }

                    // 倒计时
                    let interval = window.setInterval(() => {
                        if (--that.t <= 0) {
                        that.t = rTime;
                        that.isDisabled = false;
                        that.captcha_text = '获取验证码';

                        window.clearInterval(interval);
                    } else {
                        that.isDisabled = true;
                        that.captcha_text = '(' + that.t + 's)后重新获取';
                    }
                }, 1000);

                    that.$http.post("{!! yzWebUrl('setting.key.sendSms') !!}", {'mobile': this.form.mobile}).then(response => {

                        if (response.data.result) {
                        this.$message({
                            message: response.data.data.msg,
                            type: 'success'
                        });
                    } else {
                        this.$message({
                            message: '未获取到数据',
                            type: 'error'
                        });
                    }

                }, response => {
                        console.log(response);
                    });
                },
                change_province: function (item) {
                    let that = this;
                    that.$http.post("{!! yzWebUrl('setting.key.getcity') !!}", {'data': item}).then(response => {

                        if (response.data.result) {
                        that.opt_city = response.data.data;
                    } else {
                        this.$message({
                            message: '未获取到数据',
                            type: 'error'
                        });
                    }
                }, response => {
                        console.log(response);
                    });
                },
                change_city: function (item) {
                    let that = this;
                    that.$http.post("{!! yzWebUrl('setting.key.getarea') !!}", {'data': item}).then(response => {
                        if (response.data.result) {
                        that.opt_area = response.data.data;
                    } else {
                        this.$message({
                            message: '未获取到数据',
                            type: 'error'
                        });
                    }
                }, response => {
                        console.log(response);
                    });
                },
                reg_shop: function (type) {
                    const loading = this.$loading({
                        lock: true,
                        text: '努力注册中',
                        spinner: 'el-icon-loading',
                        background: 'rgba(0, 0, 0, 0.7)'
                    });

                    this.$http.post("{!! yzWebUrl('setting.key.index') !!}", {'upgrade': {'key':this.key, 'secret': this.secret}, 'type': type}).then(response => {
                        loading.close();

                        if (response.data.result) {

                        this.$message({
                            message: response.data.msg,
                            type: 'success'
                        });
                        window.location = response.data.data.url;
                    } else {
                        this.$message({
                            message: response.data.msg,
                            type: 'error'
                        });
                    }
                }, response => {
                        loading.close();
                        console.log(response);
                    });
                },
                onSubmit: function () {
                    this.$refs.form.validate((valid) => {
                        if (valid) {
                            const loading = this.$loading({
                                lock: true,
                                text: '努力注册中',
                                spinner: 'el-icon-loading',
                                background: 'rgba(0, 0, 0, 0.7)'
                            });

                            this.$http.post("{!! yzWebUrl('setting.key.register') !!}", {'data': this.form}).then(response => {
                                loading.close();

                                if (response.data.result) {
                                this.$message({
                                    message: response.data.msg,
                                    type: 'success'
                                });
                                window.location = response.data.data.url;
                            } else {
                                this.$message({
                                    message: response.data.msg,
                                    type: 'error'
                                });
                            }
                        }, response => {
                                loading.close();
                                console.log(response);
                            });
                        } else {
                            return false;
                }
                });
                },
                tapclickPas(){
                      let data={
                        key:this.key,
                        secret:this.secret
                      }

                    this.$http.post("{!! yzWebUrl('setting.key.reset') !!}", {'data': data}).then(res => {
                                res=res.body
                        if (res.result==1) {
                        this.key = res.data.key;
                        this.secret = res.data.secret
                        this.$message({
                            message: res.msg,
                            type: 'success'
                        });
                        }
                    })
                }
            },
            watch: {
                'form.province': function (newValue, oldValue) {
                    this.form.city = null
                    this.opt_city = [{id:0,areaname:'请选择'}];
                    this.form.area = null
                    this.opt_area = [{id:0,areaname:'请选择'}];
                },
                'form.city': function (newValue, oldValue) {
                    this.form.area = null
                    this.opt_area = [{id:0,areaname:'请选择'}];
                }
            }
        });
    </script>
@endsection
