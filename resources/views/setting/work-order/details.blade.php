@extends('layouts.base')
@section('title', '工单详情')
@section('content')
<style>
    .el-textarea__inner {
        height: 218px;
    }

    .el-upload__input {
        opacity: 0;
        width: 0;
    }
    .bigJian{
    }
    #textarea{
        border: 1px solid #ccc;
        padding: 10px;
        width:1113px;
        min-height: 218px;
        height:100%;
        overflow: hidden;
    }
    .img_grop{
        /* position: absolute; */
        /* top: 50%; */
        /* left: 50%; */
        /* transform: translate(-50%,-50%); */
    }
    .img_grop img{
        width: 100%;
        height: 100%;
    }
    .el-dialog{
        background: rgba(255,255,255,.1);
    }
</style>
<div style="margin-left:20px;">我的工单</div>
<div id="app">
    <template>
        <div class="workOrder-detail" style="margin:20px;">
            <div class="title" style="fontSize:16px;padding-bottom:15px;border-bottom:1px solid #ccc;">
                工单详情
            </div>
            <div class="title" style="fontSize:16px;padding-bottom:15px;border-bottom:1px solid #ccc;margin-top:20px;">
                工单状态
            </div>
            <div class="box" style="margin-top:20px;margin-left:20px;">
                <span v-text="first_list.status_name"></span>
                <!-- <el-button size="mini" style="margin-left:20px;" v-if="first_list.status_name!='已处理'">已处理</el-button> -->
            </div>
            <div class="title" style="fontSize:16px;padding-bottom:15px;border-bottom:1px solid #ccc;margin-top:20px;">
                工单信息
            </div>
            <div class="box" style="margin-left:30px;margin-top:10px;">
                <span>站点：</span><span v-text="site_url"></span>
                <div style="margin-top:10px;">问题标题：<span v-text="first_list.question_title"></span>
                </div>
                <div style="margin-top:10px;">问题描述：清晰的描述问题产生的操作流程，问题结果，期望的正确结果；<br>
                    如果是涉及到分销佣金、分红、返现等模式计算，要清晰的讲解设置、会员关系、正确的结算结果、错误的计算结果等；<br>
                    如果您觉得下方编辑框操作麻烦可使用附件上传按钮 直接上传Word、excel等说明文档。</div>
                <div style="margin-top:10px;border:1px solid #ccc;">
                    <el-input show-word-limit style="width:1016px;height:288px;overflow:hidden;padding:10px;" type="textarea" disabled v-html="first_list.question_describe" placeholder=""></el-input>
                </div>
            </div>
            <div class="detailInfo" style="margin-top:20px;">
                <div class="infotitle" style="fontSize:16px;border-bottom:1px solid #ccc;padding-bottom:15px;">
                    基本信息
                </div>
                <el-form ref="first_list" :model="first_list" label-width="150px" style="margin-top:20px;">
                    <el-form-item label="工单编号：" v-if="!isempty(first_list.work_order_sn)">
                        <span v-text="first_list.work_order_sn"></span>
                    </el-form-item>
                    <el-form-item label="工单分类： " v-if="!isempty(first_list.status)">
                        <span v-if="first_list.category_id==1">bug提交</span>
                        <span v-if="first_list.category_id==0">全部</span>
                        <span v-if="first_list.category_id==2">优化建议</span>
                        <span v-if="first_list.category_id==3">其他</span>
                    </el-form-item>
                    <el-form-item label="提交时间：" v-if="!isempty(first_list.created_at)">
                        <span v-text="first_list.created_at"></span>
                    </el-form-item>
                    <el-form-item label="受理时间：" v-if="!isempty(first_list.completion_time)">
                        <span v-text="first_list.completion_time"></span>
                    </el-form-item>
                    <el-form-item label="完成时间：" v-if="!isempty(first_list.updated_at)">
                        <span v-text="first_list.updated_at"></span>
                    </el-form-item>
                    <el-form-item label="处理时长：" v-if="!isempty(first_list.difference)">
                        <span  v-text="first_list.difference"></span>
                    </el-form-item>
                </el-form>

                <div class="info" v-if="first_list.has_many_comment.length>0">
                <div class="infotitle" style="fontSize:16px;border-bottom:1px solid #ccc;padding-bottom:15px;">
                    沟通记录
                </div>
                <div class="imgBox" style="margin-top:21px;">
                <div style="margin-left:130px;margin-bottom:10px;" v-for="(item,index) in first_list.has_many_comment ">
                    <div v-if="item.user_id!=0" style="margin-left:-55px;font-weight:800">
                        售后人员：
                    </div>
                    <div style="margin-left:-55px;font-weight:800" v-if="item.user_id==0">
                        用户回复：
                    </div>
                    <!-- <span v-text="item.content"></span> -->
                    <el-input v-model="item.content" type="textarea" style="width:989px;margin:10px 0" readonly="readonly"></el-input>
                    <div v-if="item.imgData.length>0" class="imgInfo" style="border:1px solid rgba(187, 187, 187, 1);width:989px;">
                    <div  v-for="(item,index) in item.imgData"  style="padding: 10px;display: inline-block;width: 200px;text-align: center;">
                        <img v-if="!item.imgShow&&item.val.indexOf('xlsx')!=-1" style="width:50px;height:50px;" src="{!! resource_get('static/images/icon/xlsx.png') !!}" alt="">
                        <img v-if="item.val.indexOf('docx')!=-1||item.val.indexOf('doc')!=-1" style="width:50px;height:50px;" src="{!! resource_get('static/images/icon/word.png') !!}" alt="">
                        <img v-if="item.val.indexOf('mp4')!=-1" style="width:50px;height:50px;" src="{!! resource_get('static/images/icon/mp4.png') !!}" alt="">
                        <img v-if="item.val.indexOf('mp3')!=-1" style="width:50px;height:50px;" src="{!! resource_get('static/images/icon/mp3.png') !!}" alt="">
                        <img v-if="item.val.indexOf('zip')!=-1" style="width:50px;height:50px;" src="{!! resource_get('static/images/icon/zip.png') !!}" alt="">
                        <img v-if="item.val.indexOf('txt')!=-1" style="width:50px;height:50px;" src="{!! resource_get('static/images/icon/txt.png') !!}" alt="">
                        <img v-if="item.defaultshow" style="width:50px;height:50px;" src="{!! resource_get('static/images/icon/defailt.png') !!}" alt="">
                        <div v-if="!item.show"  v-text="item.val" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;" ></div>
                        <!-- <div v-if="!item.show" style="margin-left: 20px;"  class="txt_title"  >( 下载 )</div> -->
                        <img v-if="!item.show" style="width:20px;height:20px;cursor:pointer;" src="{!! resource_get('static/images/icon/down.png') !!}"  @click="gotoTxt(item.val)" alt="">
                        <img   v-if="item.show"    @click="tapFimg(item.val)" :src="item.val" alt="" style="width:146px;height:129px;margin:12px 8px 24px 15px;">
                    </div>
                    </div>
                    <br>
                    <span  v-text="item.created_at"></span>
                    <div class="title" style="fontSize:16px;padding-bottom:15px;border-bottom:2px solid #333;width:989px;">

                     </div>
                    </div>
                </div>
                </div>
                 <!-- 弹出大的图片   -->
                 <el-dialog
                    :visible.sync="dialogVisible"
                    :show-close="false"
                   width="20%"
                   top="20%"
                    >
                    <div style="width:100%;" class="img_grop" v-if="dialogVisible">
                    <img :src="thumb_url" id="thumb_url" alt="">
                     </div>
                    </el-dialog>

                <div>
                    <div class="bigJian">
                    <div contentEditable="true" ref="textarea" @keyup.delete="paseTra"  id="textarea" >
                    </div>
                    </div>
                    <p style="margin-top:10px;">点击上传图片 选中输入框可直接粘贴QQ截图</p>
                    <div class="upImg" style="width:1131px;height:152px;">
                <el-upload
                    class="upload-demo"
                    action="{!!yzWebFullUrl('setting.work-order.upload-file')!!}"
                    :on-success="onSuccess"
                    :before-upload="beforeUpload"
                    :on-remove="handleRemove"
                    :auto-upload="true"
                    multiple
                    :limit="6"
                    :on-exceed="exceed"
                    :file-list="fileList"
                     style="margin: 20px 20px 0 20px;"
                    >
                    <el-button size="small" type="primary">点击上传</el-button>
                    <P class="text" style="margin-top:10px;">文件大小不能超过20M</P>
                    </el-upload>
                    </div>
                    <br/>
                    <div style="margin-left: 100px;margin-top:90px;" >
                    <el-button size="small"  @click="detailCate">追加说明</el-button>
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
        data() {
            let data={!! $data !!};
            console.log(data,'详情数据');
            if ( data.has_many_comment.length>0) {
                data.has_many_comment.map(item=>{
                 let data=[];
                 for(key in  item.thumb_url){
                     if (item.thumb_url[key]!=null) {
                        data.push(item.thumb_url[key])
                     }

                 }
                 item.imgData=data;

                 item.imgData.map((val,i)=>{
                       if(item.imgData[i].indexOf('png')!=-1||item.imgData[i].indexOf('jpg')!=-1){
                        let json={}
                         json.val=item.imgData[i];
                         json.show=true;
                         item.imgData[i]=json

                      }else if(item.imgData[i].indexOf('png')==-1&&item.imgData[i].indexOf('jpg')==-1&&item.imgData[i].indexOf('mp4')==-1&&item.imgData[i].indexOf('txt')==-1&&item.imgData[i].indexOf('zip')==-1&&item.imgData[i].indexOf('xlsx')==-1&&item.imgData[i].indexOf('docx')==-1){
                        let json={}
                         json.val=item.imgData[i];
                         json.defaultshow=true;
                         item.imgData[i]=json
                      }
                      else{
                        let json={}
                         json.val=item.imgData[i];
                         json.show=false;
                         item.imgData[i]=json
                      }
                 })
                })
            }
            return {
                site_url: 'www.wq.com',
                question_title: '',
                question_describe: '',
                first_list: data,
                dialogImageUrl: '',
                dialogVisible: false,
                disabled: false,
                domin: 'http://gy18465381.imwork.net',
                detailInfo: '', //留意信息
                category_id:'',//分类id
                work_order_id:'',//工单id
                fileList:[],//上传图片数组
                thumb_url:'',//放大的图片1
                thumb_urltwo:'',//放大的图片2
                fileListShop:[],//截图数组
                dialogVisible:false,//弹框
            }
        },
          computed: {

          },
        created() {
        },
        mounted() {

            this.paseImg()
        },
        methods: {
            paseTra(e){
                this.fileListShop.splice(this.fileListShop.length-1,1);
            },
            isempty(str){
                if ((str == null) || (str == '') || (str == undefined)) {
                        return true
                    } else {
                        return false
                    }
            },
            // 点击图片放大
            tapFimg(item){
                this.dialogVisible=true;
                this.thumb_url=item;
            },
            // 超出个数函数
            exceed(files, fileList){
                this.$message.error('上传个数已超出')
                console.log('超出了');

            },
             // 上传成功的
             onSuccess(res, file, fileList) {
                console.log(res, file, fileList, '东西啊啊');
                if (res.result == 1) {
                    this.fileList.push(res.data.thumb_url);
                    this.$message.success('上传成功')
                } else {
                    this.$message.error(res.msg)
                }

            },
            // 添加说明
            detailCate() {
                      this.detailInfo=this.$refs.textarea.innerText;
                if (this.detailInfo == ''&&this.fileListShop.length==0) {
                    this.$message.error('请输入文字信息')
                    return;
                }
                this.fileListShop.map(key=>{
                    this.fileList.push(key)
                })
                let url = window.location.href;
                url = url.split('?')
                url = url[url.length - 1].split('&')
                let id;
                url.map((item, index) => {
                    if (index == url.length - 1) {
                        id = item.split('=')
                    }
                })
                id = id[1]
                this.work_order_id=id;

                let data={
                    work_order_id:this.work_order_id,
                    content:this.detailInfo,
                    thumb_url:this.fileList,
                    work_order:1
                }
                this.$http.post('{!!yzWebFullUrl('setting.work-order.comment')!!}',data).then(res=>{
                    res=res.body;
                    if (res.result==1) {
                        this.$message.success(res.msg);
                        window.location.href = "{!! yzWebFullUrl('setting.work-order.index') !!}";
                    }else{
                        this.$message.error(res.msg);
                    }
                })

            },
            handleRemove(file, fileList) {
        console.log(file, fileList);
        this.fileList.map((item,index)=>{
            if (item==file.response.data.thumb_url) {
                delete this.fileList[index]
            }
        })
        console.log(this.fileList,'上传的数组');

      },
            beforeUpload(file){
                 console.log(file,'上传之前的操作');
                    const isLt20M = file.size / 1024 / 1024 < 20;
                      if (!isLt20M) {
                        this.$message({
                        message: '上传文件大小不能超过 20MB!',
                        type: 'warning'
                    });
                    return false
                      }
            },
            paseImg() {
                      let that=this;

                    var imgReader = function(item) {
                        var blob = item.getAsFile(),
                            reader = new FileReader();

                        // 读取文件后将其显示在网页中
                        reader.onload = function(e) {
                            let data=new FormData();
                            data.append('file',e.target.result)
                             that.$http.post('{!!yzWebFullUrl('setting.work-order.base64Upload')!!}',data,{headers: { "Content-Type": "multipart/form-data" }}).then(res=>{
                                 res=res.body;
                                 if (res.result==1) {
                                     that.$message.success('上传成功');
                                     var img = new Image();
                                    img.width=146;
                                    img.height=129;
                                    img.src = res.data.thumb_url;
                                    that.fileListShop.push(res.data.thumb_url)
                                    var box=document.getElementById('textarea')
                                    box.appendChild(img);
                                 }else{
                                    that.$message.error(res.msg);
                                 }
                             })



                        };
                        // 读取文件
                        reader.readAsDataURL(blob);
                    };
                    document.getElementById('textarea').addEventListener('paste', function(e) {
                        // 添加到事件对象中的访问系统剪贴板的接口
                        var clipboardData = e.clipboardData,
                            i = 0,
                            items, item, types;

                        if (clipboardData) {
                            items = clipboardData.items;
                            if (!items) {
                                return;
                            }
                            item = items[0];
                            // 保存在剪贴板中的数据类型
                            types = clipboardData.types || [];
                            for (; i < types.length; i++) {
                                if (types[i] === 'Files') {
                                    item = items[i];
                                    break;
                                }
                            }
                            // 判断是否为图片数据
                            if (item && item.kind === 'file' && item.type.match(/^image\//i)) {

                                imgReader(item);
                            }
                        }
                    });

            },
            // 跳转txt文件
            gotoTxt(filename) {
                let text ="hello word!"
        var pom = document.createElement("a");
        pom.setAttribute(
            "href",
            filename
          );
          pom.setAttribute("download", filename);
          if (document.createEvent) {
            var event = document.createEvent("MouseEvents");
            event.initEvent("click", true, true);
            pom.dispatchEvent(event);
          } else {
            pom.click();
          }
    },
        },
    })
</script>
@endsection
