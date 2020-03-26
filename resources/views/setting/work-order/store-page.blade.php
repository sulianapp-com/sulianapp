@extends('layouts.base')
@section('title', '工单列表')
@section('content')
<style>
    .el-upload__input {
        opacity: 0;
        width: 0;
    }
</style>
<div style="margin-left:20px;margin-top: 10px;">我的工单</div>
<div id="app">
    <template>
        <div class="workOrder" style="margin:20px;">
            <div class="title" v-html="title" style="fontSize:16px;border-bottom:1px solid #ccc;padding-bottom:15px;">

            </div>
            <div class="bigbox">
                <div style="margin:40px auto;width:400px;">
                    <el-steps :space="200"  :active="active" finish-status="finish ">
                        <el-step title="选择问题分类"></el-step>
                        <el-step title="创建工单"></el-step>
                    </el-steps>
                    <div v-if="issubshow">
                        <el-select v-model="value" placeholder="请选择" style="margin-top:20px;">
                            <el-option v-for="item in category_list" :key="item.value" :label="item.label" :value="item.value">
                            </el-option>
                        </el-select>
                        <div style="margin-top:35px;">
                            <el-button plain @click="tapOne">下一步</el-button>
                        </div>
                    </div>


                </div>
            </div>
            <div class="infoBox" v-if="!issubshow">
                <div class="infotitle" style="fontSize:16px;border-bottom:1px solid #ccc;padding-bottom:15px;">
                    基本信息
                </div>
                <div class="box" style="margin-left:30px;margin-top:10px;">
                    <span>站点：</span><span v-text="site_url"></span>
                    <div style="margin-top:10px;">问题标题：<el-input v-model="question_title" placeholder="请输入内容" style="width:783px;"></el-input>
                    </div>
                    <div style="margin-top:10px;">问题描述：清晰的描述问题产生的操作流程，问题结果，期望的正确结果；<br>
                        如果是涉及到分销佣金、分红、返现等模式计算，要清晰的讲解设置、会员关系、正确的结算结果、错误的计算结果等；<br>
                        如果您觉得下方编辑框操作麻烦可使用附件上传按钮 直接上传Word、excel等说明文档。</div>
                    <div style="margin-top:10px;">

                        <tinymce v-model="question_describe"></tinymce>
                        <div class="form-group" style="margin-bottom:20px;">
                            <el-upload style="margin-top:20px;" class="upload-demo" :on-remove="removeUP" action="{!!yzWebFullUrl('setting.work-order.upload-file')!!}" :on-success="onSuccess" :before-remove="beforeRemove" multiple  :limit="3"  :before-upload="beforeUpload" :on-exceed="handleExceed" :file-list="fileList">
                                <el-button size="small" type="primary">点击上传</el-button>
                                <div slot="tip" class="el-upload__tip">支持上传excel、word、txt和图片等文件</div>
                            </el-upload>
                        </div>

                    </div>
                    <div class="password_box" style="margin-top:70px;">
                        <div class="infotitle" style="fontSize:14px;border-bottom:1px solid #ccc;padding-bottom:15px;">
                            加密信息(此部分信息将做加密处理，为方便您的问题尽快处理请放心填写)
                        </div>
                        <el-form ref="first_list" :model="first_list" label-width="150px" style="margin-top:20px;">
                            <el-form-item label="站点网址：">
                                <el-input v-model="first_list.website_url" :disabled="nameShow" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="创始人账号：">
                                <el-input v-model=" first_list.founder_account" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="创始人密码：">
                                <el-input type="password" v-model=" first_list.founder_password" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="服务器IP：">
                                <el-input v-model=" first_list.server_ip" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="服务器root密码：">
                                <el-input type="password" v-model=" first_list.root_password" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="服务器SSH端口：">
                                <el-input v-model=" first_list.ssh_port" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="数据库访问地址：">
                                <el-input v-model=" first_list.database_address" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="数据库用户名：">
                                <el-input v-model=" first_list.database_username" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="数据库密码：">
                                <el-input type="password" v-model=" first_list.database_password" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item label="网站目录位置：">
                                <el-input v-model=" first_list.root_directory" style="width:760px;"></el-input>
                            </el-form-item>
                            <el-form-item>
                                如果您的服务器由官方部署，可不填写网站目录位置！
                            </el-form-item>
                            <el-form-item>
                                <div class="infoUser" style="margin-top:17px;margin-left: -70px;">
                                    <div class="infotitle" style="fontSize:14px;border-bottom:1px solid #ccc;padding-bottom:15px; ">
                                        联系方式
                                    </div>
                                    <el-form-item label="联系QQ：" style="margin-top:20px;">
                                        <el-input v-model="first_list.qq" style="width:760px;"></el-input>
                                    </el-form-item>
                                    <el-form-item label="手机号：">
                                        <el-input v-model="first_list.mobile" style="width:760px;"></el-input>
                                    </el-form-item>
                                    <el-form-item style="margin-left: 70px;margin-top: 20px;">
                                        <el-button @click="submit">提交</el-button>
                                    </el-form-item>
                                </div>
                            </el-form-item>
                        </el-form>
                    </div>

                </div>
            </div>
        </div>
    </template>
</div>
<script src="{{resource_get('static/yunshop/tinymce4.7.5/tinymce.min.js')}}"></script>
<script src="{{resource_get('static/yunshop/tinymceTemplate.js')}}"></script>
<script>
    var vm = new Vue({

        el: "#app",
        delimiters: ['[[', ']]'],
        data() {
            let site_url = {!! $site_url !!};
            let category_list = {!! $category_list !!};
            let first_list ={!! $first_list !!};
            category_list.map(item => {
                item.value = item.id;
                item.label = item.name;
            })
            console.log(first_list,'接的数据');
            // 判断为空的时候
               if (first_list.length==0) {
                   return;
               }
            if (Object.keys(first_list).length!=0) {
                for(let key  in first_list){
                if (first_list[key]==false) {
                    first_list[key]=''
                }
             }
            }

            return {
                category_list: category_list,
                value: 0,
                issubshow: true,
                active: 1,
                title: '提交工单',
                question_title: '', //问题标题,
                question_describe: '', //问题描述
                site_url: site_url, //站点url
                first_list: first_list,
                nameShow: false,
                fileList: [],
                category_id: '', //分类id
                thumb_url:[],//文件链接数组
            }
        },
        created() {
            // this.getCata()
            window.addEventListener('beforeunload', e => {
                window.onbeforeunload = null
            });

        },
        methods: {
            tapOne() {
                if (this.value == ''&&this.value==0) {
                    this.$message.error('请选择分类');
                    return;
                }
                this.category_list.map(item => {
                    if (item.value == this.value) {
                        this.category_id = item.id
                        this.title += '--' + item.label
                    }
                })

                this.active = 2
                this.issubshow = false
            },
            // 上传文件之前的函数
            beforeUpload(file){
                 console.log(file,'文件')
            },
             delHtmlTag(str){
              return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
                  },
            // 提交
            submit() {
                if (this.question_title=='') {
                    this.$message.error('请输入问题标题');
                    return;
                }
                if (this.question_describe=='') {
                    this.$message.error('请输入问题描述');
                    return;
                }else{
                    this.question_describe=this.question_describe.replace(/&nbsp;/ig, "<br>");
                }
                if (this.first_list.website_url=='') {
                    this.$message.error('请输入站点网址');
                    return;
                }
                if (this.first_list.founder_account=='') {
                    this.$message.error('请输入创始人账号');
                    return;
                }
                if (this.first_list.founder_password=='') {
                    this.$message.error('请输入创始人密码');
                    return;
                }
                if (this.first_list.server_ip=='') {
                    this.$message.error('请输入服务器ip地址');
                    return;
                }
                if (this.first_list.root_password=='') {
                    this.$message.error('请输入root密码');
                    return;
                }
                if (this.first_list.ssh_port=='') {
                    this.$message.error('请输入端口');
                    return;
                }
                if (this.first_list.database_address=='') {
                    this.$message.error('请输入数据库访问地址');
                    return;
                }
                if (this.first_list.database_username=='') {
                    this.$message.error('请输入数据库用户名');
                    return;
                }
                if (this.first_list.database_password=='') {
                    this.$message.error('请输入数据库密码');
                    return;
                }
                if (this.first_list.qq=='') {
                    this.$message.error('请输入qq');
                    return;
                }
                if (this.first_list.mobile=='') {
                    this.$message.error('请输入手机号');
                    return;
                }
                console.log(this.first_list, this.forminfo, '提交的form表单');
                console.log(this.category_id, '分类ID');
                this.question_describe=this.delHtmlTag(this.question_describe);
                let data = {
                    category_id: this.category_id,
                    question_title: this.question_title,
                    question_describe: this.question_describe,
                    first_list: this.first_list,
                    thumb_url:this.thumb_url
                };
                this.$http.post('{!!yzWebFullUrl('setting.work-order.store')!!}', {data}).then(res => {
                    console.log(res, '99999');
                    res = res.body;
                    if (res.result == 1) {
                        this.$message.success(res.msg);
                        window.location.href = "{!! yzWebFullUrl('setting.work-order.index') !!}";
                    } else {
                        this.$message.error(res.msg)
                    }
                    console.log(res, '数据');
                })
            },
            edidInfo() {
                console.log('修改显示');
                // this.form.name = ''
                this.nameShow = true
            },
            // 上传成功的
            onSuccess(res, file, fileList) {
                if (res.result == 1) {
                    this.$message.success('上传成功')
                } else {
                    this.$message.error(res.msg)
                }

            },
            removeUP(file, fileList){
                 fileList.map(item=>{
                    this.thumb_url.push(item.response.data.thumb_url);
                 })
            },
            beforeRemove(file, fileList) {
                return this.$confirm(`确定移除 ${ file.name }？`);
            },
            handleExceed(files, fileList) {
                this.$message.warning(`当前限制选择 3 个文件，本次选择了 ${files.length} 个文件，共选择了 ${files.length + fileList.length} 个文件`);
            },


        },
    })
</script>
@endsection
