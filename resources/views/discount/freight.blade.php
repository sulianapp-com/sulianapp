@extends('layouts.base')
@section('title', '折扣运费设置')
@section('content')
    <style>
        .el-form-item__label{padding-right:30px;}
    </style>
    <div class="w1200 m0a">
        @include('layouts.tabs')
        
        <div class="rightlist">
            <div id="app"  v-loading="submit_loading">
                <template>
                    <el-form ref="form" :model="form" :rules="rules" label-width="15%">
                        <el-form-item label="分类批量" prop="batch_list">
                            <template v-for="(item,index) in form.batch_list">
                                <el-input :value="item.new_name" style="width:60%;padding:10px 0;" disabled></el-input>
                                <a v-bind:href="'{{ yzWebUrl('discount.batch-dispatch.update-freight', array('id' => '')) }}'+[[form.batch_list[index].id]]">
                                    <el-button>编辑</el-button>
                                </a>
                                <el-button type="danger" icon="el-icon-close" @click="delBatch(index,form.batch_list[index].id)"></el-button>
                            </template><br>
                            <a href="{{ yzWebFullUrl('discount.batch-dispatch.freight-set') }}">
                                <el-button type="primary">添加批量运费</el-button>
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
                 let batch_list = JSON.parse('{!! $category?:'{}' !!}');
               // let batch_list = [
               //      {
               //      category_ids: [
               //          {id: 20,name: "1.1"},
               //      ],
               //      // created_at: "2019-02-19 13:43:28",
               //      // discount_method: 1,
               //      id: 18,
               //      // level_discount_type: 1,
               //      new_name: "[ID:20][分类:1.1]",
               //      // uniacid: 2
               //      // updated_at: "2019-02-19 13:43:28",
               //  }
               // ];
                for(var i=0;i<batch_list.length;i++){
                    batch_list[i].new_name=[];
                    for(var j=0;j<batch_list[i].category_ids.length;j++){
                        batch_list[i].new_name[j] = "[ID:"+batch_list[i].category_ids[j].id+"][分类:"+batch_list[i].category_ids[j].name+"]";
                    }
                    batch_list[i].new_name = batch_list[i].new_name.join(",");
                }
                return{
                    form:{
                        batch_list:batch_list,
                    },
                    loading: false,
                    submit_loading: false,
                    rules: {

                    },
                }
            },
            methods: {
                delBatch(index,id){
                    if(!id){
                        this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                            this.form.batch_list.splice(index,1);
                            this.$message({type: 'success',message: '删除成功111!'});
                        }).catch(() => {
                            this.$message({type: 'info',message: '已取消删除'});
                        });
                    }
                    else{
                        this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
                            this.table_loading=true;
                            this.$http.post('{!! yzWebFullUrl('discount.batch-dispatch.delete-freigh') !!}',{id:id}).then(function (response) {
                                    console.log(response.data);
                                    if (response.data.result) {
                                        this.form.batch_list.splice(index,1);
                                        this.$message({type: 'success',message: '删除成功!'});
                                    }
                                    else{
                                        this.$message({type: 'error',message: response.data.msg});
                                    }
                                    this.table_loading=false;
                                },function (response) {
                                    this.$message({type: 'error',message: response.data.msg});
                                    this.table_loading=false;
                                }
                            );
                        }).catch(() => {
                            this.$message({type: 'info',message: '已取消删除'});
                        });

                    }
                },
                // settingBatch(index,id) {
                //     console.log(index,id);
                //     window.location.href='{!! yzWebFullUrl('discount.batch-discount.store') !!}';
                // },
            },
        });
    </script>
@endsection



