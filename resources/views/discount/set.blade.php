@extends('layouts.base')
@section('title', '折扣设置')
@section('content')
    <style>
        #app{padding:30px 0;}
        .el-form-item__label{padding-right:30px;}
    </style>

    <div class="w1200 m0a">
        @include('layouts.tabs')

        <div class="rightlist">
            <div id="app"  v-loading="submit_loading">
                <template>
                    <el-form ref="form" :model="form" :rules="rules" label-width="15%">
                        <el-form-item label="选择分类" prop="classification">
                            <el-input :value="form.classification" style="width:60%;" disabled></el-input>
                            <el-button type="primary" @click="visDia()">选择分类</el-button>
                            <el-dialog title="选择分类" :visible.sync="dialogTableVisible" @close="choose()">
                                <!-- :placeholder="form.classification" -->
                                <el-select
                                    value-key="id"
                                    @change="change"
                                    v-model="form.search_categorys"
                                    filterable
                                    multiple
                                    remote
                                    reserve-keyword
                                    
                                    :remote-method="loadCategorys"
                                    :loading="loading"
                                    style="width:100%">
                                    <el-option
                                        v-for="item in categorys"
                                        :key="item.id"
                                        :label="'[ID:'+item.id+'][分类:'+item.name+']'"
                                        :value="item"
                                        >
                                        <!-- :value="'[ID:'+item.id+'][分类:'+item.name+']'" -->
                                    </el-option>
                                </el-select>
                                <!-- <el-button @click="search()">搜索</el-button><br> -->
                                {{--<div v-for="(item,index) in list">[[item.name]]</div>--}}
                                <span slot="footer" class="dialog-footer">
                                    <!-- <el-button @click="dialogVisible = false">取 消</el-button> -->
                                    <el-button type="primary" @click="choose()">确 定</el-button>
                                </span>
                            </el-dialog>
                        </el-form-item>

                        <el-form-item label="折扣类型" prop="type">
                            <el-radio v-model="form.discount_type" :label="1">会员等级</el-radio>
                        </el-form-item>
                        <el-form-item label="折扣方式" prop="method">
                            <el-radio v-model="form.discount_method" :label="1">折扣</el-radio>
                            <el-radio v-model="form.discount_method" :label="2">固定金额</el-radio>
                        </el-form-item>
                        <el-form-item prop="">
                            <template v-for="(item,index) in member_list">
                                <el-input type="number" v-model.number="form.discount_value[item.id]" style="width:70%;padding:10px 0;">
                                    <template slot="prepend">[[item.level_name]]</template>
                                    <template slot="append" v-if="form.discount_method==1">折</template>
                                    <template slot="append" v-if="form.discount_method==2">元</template>
                                </el-input>
                            </template>
                        </el-form-item>

                    <el-form-item>
                        <a href="#">
                            <el-button type="success" @click="submitForm('form')">
                                提交
                            </el-button>
                        </a>
                        <a href="#">
                            <el-button @click="goBack()">
                                返回列表
                            </el-button>
                        </a>
                    </el-form-item>
                    </el-form>
                </template>
            </div>
        </div>
    </div>

    <script>
        var vm = new Vue({
        el:"#app",
        delimiters: ['[[', ']]'],
            data() {
                let member_list = {!! $levels?:'{}' !!};
                let url = {!! $url !!};
                let categoryDiscount = {!! $categoryDiscount?:'{}' !!};
                console.log(categoryDiscount,456);
                let form ={
                        discount_type:1,
                        discount_method:1,
                        discount_value:[],
                        category_ids:[],
                        classification:"",
                        search_categorys:"",
                        ...categoryDiscount,
                    };

                let classic =[];
                form.classification = classic.join(",");

                // var checkNumber = (rule, value, callback) => {
                //     if (!Number.isInteger(value)) {
                //         callback(new Error('请输入数字'));
                //     }
                //     setTimeout(() => {
                //         callback();
                //     }, 1000);
                // };

                return{
                    url:url,
                    form:form,
                    classic:classic,
                    member_list:member_list,
                    categorys:[],
                    dialogVisible:true,
                    dialogTableVisible:false,
                    loading: false,
                    submit_loading: false,
                    rules: {
                        // discount_value: [
                        //     { required: false,type: 'number', message: '请输入数字'},
                        //     { type: 'number', min: 1, max: 99999, message: '请输入1-99999'},
                            // { validator : checkNumber }
                        // ],
                        // name: [
                        //     { required: true,message: '请输入分类名称', trigger: 'blur' },
                        //     { max : 45,message: '不能超过45个字符', }
                        // ],
                        // thumb: [
                        //     { required: true, message: '请选择图片'},
                        //  ]
                    },
                }
            },
            mounted() {
                if(this.form.category_ids) {
                    for(var j=0;j<this.form.category_ids.length;j++){
                        this.classic[j] = "[ID:"+this.form.category_ids[j].id+"][分类："+this.form.category_ids[j].name+"]";
                    }
                    console.log(this.classic)
                }
                this.form.classification = this.classic.join(",");
                console.log(this.form.classification);
                this.form.search_categorys = this.form.category_ids;
                console.log(this.form.search_categorys);
            },
            methods: {
                change(item){
                    console.log(item,"44545");
                    this.form.category_ids = item;
                    console.log(this.form.category_ids,123)
                    // for(var k=0;k<item.length;k++){
                    //     this.classic[k] = "[ID:"+item[k].id+"][分类："+item[k].name+"]";
                    // }
                    
                    // if(this.form.search_categorys.indexOf(item) == -1){
                    //     this.form.search_categorys.push(item)
                    // }
                    
                    // const categorys = this.form.search_categorys.map(v => {
                    //     if(typeof v !== "string" && v.id){
                    //         delete v.thumb
                    //         v = {...v};
                    //         this.form.category_ids.push(v);
                         
                    //         return `[ID:${v.id}][分类：${v.name}]`;
                    //     }
                    //     return v;
                    // })
                   
                    // // 去重
                    // this.form.search_categorys = [...new Set(categorys)]
                    
                    // this.form.classification = this.form.search_categorys.join(",");
                },
                visDia(){
                    for(var i=0;i<this.form.category_ids.length;i++){
                        this.form.search_categorys[i]=`[ID:${this.form.category_ids[i].id}][分类：${this.form.category_ids[i].name}]`
                        // this.form.search_categorys[i] = {id:this.form.category_ids[i].id,name:this.form.category_ids[i].name}
                    }
                    this.dialogTableVisible=true;
                },
                choose(){
                    this.dialogTableVisible=false;
                    for(let i=0;i<this.form.category_ids.length;i++){
                        if(typeof this.form.category_ids[i]=='string'){
                           let ids = parseInt(this.form.category_ids[i].substring(4));
                           let index = this.form.category_ids[i].lastIndexOf("类");
                           let cas = this.form.category_ids[i].substring(index+2,this.form.category_ids[i].length-1);
                           this.form.category_ids[i] = {},
                           this.form.category_ids[i].id = ids;
                           this.form.category_ids[i].name = cas;
                        }
                    }
                    // var k=[];
                    // for(let i=0;i<this.form.category_ids.length-1;i++){
                    //     for(let j=i+1;j<this.form.category_ids.length;j++){
                    //         if(this.form.category_ids[i].id == this.form.category_ids[j].id){
                    //             k.push(j);
                    //             // this.form.category_ids.splice(j+1,1);
                    //         }
                    //     }
                    //     console.log(k);
                    //     // this.form.category_ids.splice(k+1,1);
                    // }
                    // for(let i=0;i<k.length;i++){
                    //     this.form.category_ids.splice(k[i],1);
                    // }
                    // for(let i=0;i<this.form.category_ids.length-1;i++)
                     this.classic=[];
                        for(var j=0;j<this.form.category_ids.length;j++){
                            console.log(this.form.category_ids)
                            this.classic[j] = "[ID:"+this.form.category_ids[j].id+"][分类："+this.form.category_ids[j].name+"]";
                            console.log(this.classic)
                        }
                    this.form.classification = this.classic.join(",");
                },
                goBack() {
                    window.location.href='{!! yzWebFullUrl('discount.batch-discount.index') !!}';
                },
                loadCategorys(query) {
                    if (query !== '') {
                        this.loading = true;
                        this.$http.get("{!! yzWebUrl('discount.batch-discount.select-category', ['keyword' => '']) !!}" + query).then(response => {
                            this.categorys = response.data.data;
                            this.data=response.data.data;
                            this.loading = false;
                        }, response => {
                            console.log(response);
                        });
                    } else {
                        this.categorys = [];
                    }
                },
                submitForm(formName) {
                    // if(this.form.discount_method == 1){
                    //     for(let i=0;i<this.member_list.length;i++){
                    //         if(this.form.discount_value[i]<10||this.form.discount_value[i]>0){
                    //             this.$message({message: "折扣数值不能大于10或者小于0",type: 'error'});
                    //             return false;
                    //         }
                    //     }
                    // }

                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            this.submit_loading = true;
                                console.log(this.form);
                            this.$http.post(this.url,{'form_data':this.form}).then(response => {

                                console.log(response,'131313')
                                if (response.data.result) {
                                    this.$message({type: 'success',message: '操作成功!'});
                                     window.location.href='{!! yzWebFullUrl('discount.batch-discount.index') !!}';
                                } else {
                                    this.$message({message: response.data.msg,type: 'error'});
                                    this.submit_loading = false;
                                }
                            },response => {
                                this.submit_loading = false;
                            });
                        }
                        else {
                            console.log('error submit!!');
                            return false;
                        }
                    });
                },

            },
        });
    </script>
@endsection



