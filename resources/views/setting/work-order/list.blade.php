@extends('layouts.base')
@section('title', '工单列表')
@section('content')
<div id="app">
      <template>
            <div class="workOrder" style="margin:20px;">
                  <div class="title" style="fontSize:16px;padding-bottom:15px;">
                        我的工单
                  </div>
                  <div class="search_box">
                        <el-select v-model="value" placeholder="问题分类">
                              <el-option  v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
                              </el-option>
                        </el-select>
                        <el-select v-model="valuetwo" placeholder="处理状态" style="margin-left:5px;">
                              <el-option v-for="item in optionstwo" :key="item.value" :label="item.label" :value="item.value">
                              </el-option>
                        </el-select>
                        <el-input v-model="woker_sn" placeholder="工单编号/问题标题" style="width:200px;height:30px;margin-left:5px;"></el-input>
                        <el-select v-model="valuethree" @change="cateChange"placeholder="请选择" style="margin-left:5px;">
                              <el-option  v-for="item in optionsthree" :key="item.value" :label="item.label" :value="item.value">
                              </el-option>
                        </el-select>
                        <el-date-picker
                                  v-if="timeshow"
                              v-model="value1"
                              type="datetimerange"
                              value-format="yyyy-MM-dd HH:mm:ss"
                              range-separator="至"
                              start-placeholder="开始日期"
                              end-placeholder="结束日期"
                              style="margin-left:5px;"
                              align="right">
                        </el-date-picker>
                        <!-- <el-date-picker   v-model="value1" type="datetime" placeholder="选择日期时间" > -->
                        </el-date-picker>
                        <el-button type="primary" style="backgroud:#83c785;color:#fff;margin-left:5px;" icon="el-icon-search" @click="searchAll">搜索</el-button>
                        <el-button @click="submit">提交工单</el-button>
                  </div>
                  <div class="table_box" style="margin-top:20px;">
                        <el-table :data="tableData" border style="width: 100%">
                              <el-table-column prop="work_order_sn" label="工单编号" width="227">
                              </el-table-column>
                              <el-table-column prop="question_title" label="问题标题" width="293">
                              </el-table-column>
                              <el-table-column prop="created_at" label="提交时间">
                              </el-table-column>
                              <el-table-column prop="completion_time" label="完成时间">
                              </el-table-column>
                              <el-table-column prop="difference" label="处理时长">
                              </el-table-column>
                              <el-table-column prop="status_name" label="处理状态">
                              </el-table-column>
                              <el-table-column prop="has_one_admin_user.name" label="售后人员">
                              </el-table-column>
                              <el-table-column prop="address" label="操作">
                                    <template slot-scope="scope">
                                          <el-button size="mini" @click="catchInfo(scope.row)">查看详情</el-button>
                                    </template>
                              </el-table-column>

                        </el-table>
                  </div>
                  <div class="pageclass" style="margin-top: 20px;">
                  <el-pagination
                        @current-change="handleCurrentChange"
                        :current-page.sync="currentPage"
                        :page-size="PageSize"
                        layout="total, prev, pager, next,jumper"
                        :total="total">
                  </el-pagination>
                  </div>
            </div>
      </template>
</div>
<script>
      var vm = new Vue({
            el: "#app",
            data() {
                   let category_list={!! $category_list !!};
                   let data={!! $data !!};
                   let  status_list={!! $status_list !!};
                   category_list.map(item=>{
                         item.label=item.name;
                         item.value=item.id;
                   })
                   status_list.map(val=>{
                         val.value=val.id;
                         val.label=val.name;
                   })
                   console.log(data,'列表数据');
                   console.log(status_list,'状态列表');
                   console.log(category_list,'分类列表');
                  return {
                        options:category_list ,
                        value: 0,
                        valuetwo: 0,
                        optionstwo:status_list,
                        woker_sn: '',
                        optionsthree: [{
                              value: '1',
                              label: '搜索工单时间'
                        }, {
                              value: '0',
                              label: '工单时间不限'
                        }],
                        valuethree: '0',
                        value1: '',
                        tableData: data.data,
                        url:'http://gy18465381.imwork.net',
                        domin:'127.0.0.1',
                        category_id:'0',//分类id
                        currentPage:data.current_page,//当前页
                        total:data.total,//总条数
                        PageSize:15,//每页显示多少条
                        timeshow:false,//默认显示时间选择器
                  }
            },
            created() {
            },
            methods: {
                  // 分类改变
                  cateChange(val){
                    if (val==0) {
                          this.timeshow=false;
                    }else{
                        this.timeshow=true;
                    }
                  },
                    getTable(){
                          let starttime=new Date(this.value1[0]).getTime();
                          let endtime=new Date(this.value1[1]).getTime();
                              let data={
                                    category_id:this.value,
                                    status:this.valuetwo,
                                    work_order_sn:this.woker_sn,
                                    has_time_limit:this.valuethree,
                                    start_time:starttime,
                                    end_time:endtime,
                                    page:this.currentPage
                              }
                      this.$http.post('{!!yzWebFullUrl('setting.work-order.search')!!}',{data}).then(res=>{
                            res=res.body;
                            if (res.result==1) {
                                  console.log(res.data,'数据');

                                  this.tableData=res.data.data;
                                  this.total=res.data.total;
                                  this.currentPage=res.data.current_page;

                            }else{
                                  this.$message.error(res.msg)
                            }

                      })
                    },
                  submit() {
                        console.log(this.input, '輸入框');

                        console.log('提交工彈');
                        window.location.href = "{!! yzWebFullUrl('setting.work-order.store-page') !!}";
                  },
                  handleCurrentChange(val) {
                        this.currentPage=val;
                        this.getTable()
                  },
                  // 搜索
                  searchAll(){
                        this.currentPage=1;
                        this.getTable();

                  },
                  catchInfo(row){
                      console.log(row,'行');
                      window.location.href = "{!! yzWebFullUrl('setting.work-order.details') !!}"+'&id='+row.id;
                  }
            },
      })
</script>
@endsection
