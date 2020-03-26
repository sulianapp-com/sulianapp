@extends('layouts.base')
@section('title', "商品列表")
@section('content')
    <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/vue-goods.css')}}"/>

    <div id="qrcode" ref="qrcode" style="display:none;"></div>
    <div class="rightlist">
        <div id="app" v-cloak v-loading="all_loading">
            <template>
                <div class="second-list">
                    <div class="third-list">
                        <div class="form-list">
                            <el-form :inline="true" :model="search_form" ref="search_form" style="margin-left:10px;">
                                <el-row>
                                    <el-form-item label="" prop="">
                                        <el-select v-model="search_form.status" placeholder="请选择商品状态" clearable>
                                            <el-option v-for="item in status_list" :key="item.id" :label="item.name"
                                                       :value="item.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="" prop="">
                                        <el-select v-model="search_form.sell_stock" placeholder="请选择售中库存" clearable>
                                            <el-option v-for="item in sell_stock_list" :key="item.id" :label="item.name"
                                                       :value="item.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-select v-model="search_form.id_v1" placeholder="请选择一级分类" clearable
                                                   @change="changeV1()">
                                            <el-option v-for="item in category_list" :key="item.id" :label="item.name"
                                                       :value="item.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-select v-model="search_form.id_v2" placeholder="请选择二级分类" clearable
                                                   @change="changeV2()">
                                            <el-option v-for="item in category_list_v2" :key="item.id"
                                                       :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item>
                                        <el-select v-model="search_form.id_v3" placeholder="请选择三级分类" clearable
                                                   v-if="catlevel==3">
                                            <el-option v-for="item in category_list_v3" :key="item.id"
                                                       :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="" prop="">
                                        <el-select v-model="search_form.brand_id" placeholder="请选择品牌" clearable>
                                            <el-option v-for="item in brands_list" :key="item.id" :label="item.name"
                                                       :value="item.id"></el-option>
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item label="" prop="keyword">
                                        <el-input v-model="search_form.keyword" placeholder="请输入商品ID或关键字"></el-input>
                                    </el-form-item>
                                    <el-form-item label="价格区间" prop="">
                                        <el-input v-model="search_form.min_price" placeholder="最低价"
                                                  style="width:150px;"></el-input>
                                        至
                                        <el-input v-model="search_form.max_price" placeholder="最高价"
                                                  style="width:150px;"></el-input>
                                    </el-form-item>
                                    <el-form-item label="商品类型" prop="leader_name">
                                        <el-checkbox v-model.number="search_form.is_new" :true-label="1"
                                                     :false-label="0">新品
                                        </el-checkbox>
                                        <el-checkbox v-model.number="search_form.is_hot" :true-label="1"
                                                     :false-label="0">热卖
                                        </el-checkbox>
                                        <el-checkbox v-model.number="search_form.is_recommand" :true-label="1"
                                                     :false-label="0">推荐
                                        </el-checkbox>
                                        <el-checkbox v-model.number="search_form.is_discount" :true-label="1"
                                                     :false-label="0">促销
                                        </el-checkbox>
                                    </el-form-item>

                                    <a href="#">
                                        <el-button type="primary" icon="el-icon-search" @click="search(1)">搜索
                                        </el-button>
                                    </a>
                                    </el-col>
                                </el-row>
                            </el-form>
                        </div>
                        <div class="table-list">
                            <div style="margin-left:10px;">
                                <el-checkbox v-model.number="is_all_choose" :true-label="1" :false-label="0"
                                             @change="allChoose">[[is_all_choose==1?'全不选':'全选']]
                                </el-checkbox>
                                <el-button size="small" @click="batchPutAway(1)">批量上架</el-button>
                                <el-button size="small" @click="batchPutAway(0)">批量下架</el-button>
                                <el-button size="small" @click="batchDestroy">批量删除</el-button>
                            </div>
                            <div>
                                <template>
                                    <!-- 表格start -->
                                    <el-table :data="goods_list" style="width: 100%"
                                              :class="table_loading==true?'loading-height':''"
                                              v-loading="table_loading">
                                        <el-table-column prop="id" label="选择" width="60" align="center">
                                            <template slot-scope="scope">
                                                <el-checkbox v-model.number="scope.row.is_choose" :true-label="1"
                                                             :false-label="0"
                                                             @change="oneChange(scope.row)"></el-checkbox>
                                            </template>
                                        </el-table-column>
                                        <el-table-column prop="id" label="ID" width="70"
                                                         align="center"></el-table-column>
                                        </el-table-column>
                                        <el-table-column prop="member_name" label="排序" max-width="80" align="center">
                                            <template slot-scope="scope">
                                                <el-popover class="item" placement="top" effect="light">
                                                    <div style="text-align:center;">
                                                        <el-input v-model="change_sort" size="small"
                                                                  style="width:100px;"></el-input>
                                                        <el-button size="small"
                                                                   @click="confirmChangeSort(scope.row.id)">确定
                                                        </el-button>
                                                    </div>
                                                    <a slot="reference">
                                                        <i class="el-icon-edit edit-i" title="点击编辑排序"
                                                           @click="editTitle(scope.$index,'sort')"></i>
                                                    </a>
                                                </el-popover>
                                                [[scope.row.display_order]]
                                            </template>
                                        </el-table-column>
                                        <el-table-column prop="total" label="商品" width="60" align="center">
                                            <template slot-scope="scope">
                                                <img :src="scope.row.thumb" style="width:50px;height:50px;">
                                            </template>
                                        </el-table-column>
                                        <el-table-column prop="down_time" label="" min-width="180" align="left"
                                                         class="edit-cell">
                                            <template slot-scope="scope">
                                                <el-popover class="item" placement="top" effect="light">
                                                    <div style="text-align:center;">
                                                        <div style="text-align:left;margin-bottom:10px;font-weight:900">
                                                            修改商品标题
                                                        </div>
                                                        <el-input v-model="change_title" style="width:400px"
                                                                  size="small"></el-input>
                                                        <el-button size="small"
                                                                   @click="confirmChange(scope.row.id,'title')">确定
                                                        </el-button>
                                                    </div>
                                                    <a slot="reference">
                                                        <i class="el-icon-edit edit-i" title="点击编辑"
                                                           @click="editTitle(scope.$index,'title')"></i>
                                                    </a>
                                                </el-popover>
                                                [[scope.row.title]]
                                            </template>
                                        </el-table-column>
                                        <el-table-column prop="member_num" label="价格" max-width="80" align="center">
                                            <template slot-scope="scope">
                                                <el-popover class="item" placement="top" effect="light"
                                                            :disabled="scope.row.has_option==1">
                                                    <div style="text-align:center;">
                                                        <el-input v-model="change_price" size="small"
                                                                  style="width:100px;"></el-input>
                                                        <el-button size="small"
                                                                   @click="confirmChange(scope.row.id,'price')">确定
                                                        </el-button>
                                                    </div>
                                                    <a slot="reference">
                                                        <i class="el-icon-edit edit-i"
                                                           :title="scope.row.has_option==1?'多规格不支持快速修改':'点击编辑'"
                                                           @click="editTitle(scope.$index,'price')"></i>
                                                    </a>
                                                </el-popover>
                                                ￥[[scope.row.price]]
                                            </template>
                                        </el-table-column>
                                        <el-table-column label="库存" align="center" max-width="80">
                                            <template slot-scope="scope">
                                                <el-popover class="item" placement="top" effect="light"
                                                            :disabled="scope.row.has_option==1">
                                                    <div style="text-align:center;">
                                                        <el-input v-model="change_stock" size="small"
                                                                  style="width:100px;"></el-input>
                                                        <el-button size="small"
                                                                   @click="confirmChange(scope.row.id,'stock')">确定
                                                        </el-button>
                                                    </div>
                                                    <a slot="reference">
                                                        <i class="el-icon-edit edit-i"
                                                           :title="scope.row.has_option==1?'多规格不支持快速修改':'点击编辑'"
                                                           @click="editTitle(scope.$index,'stock')"></i>
                                                    </a>
                                                </el-popover>
                                                [[scope.row.stock]]
                                            </template>
                                        </el-table-column>
                                        <el-table-column prop="real_sales" label="销量" width="70"
                                                         align="center"></el-table-column>

                                        <el-table-column label="状态" prop="status_message" align="center">
                                            <template slot-scope="scope">
                                                [[scope.row.status?'上架':'下架']]
                                                <el-switch v-model="scope.row.status" :active-value="1"
                                                           :inactive-value="0"
                                                           @change="putAway(scope.row.id,scope.$index)"></el-switch>
                                            </template>
                                        </el-table-column>
                                        <el-table-column label="操作" width="300" align="center">
                                            <template slot-scope="scope">
                                                <div class="table-option">
                                                    <el-popover class="item" placement="left" effect="light"
                                                                trigger="hover">
                                                        <div style="text-align:center;">
                                                            <img :src="img" alt=""
                                                                 style="margin:10px;width:100px;height:100px;">
                                                        </div>
                                                        <a slot="reference" @mouseover="listCode(scope.$index)">推广链接</a>
                                                    </el-popover>&nbsp;&nbsp;
                                                    <a :href="'{{ yzWebFullUrl('goods.goods.copy', array('id' => '')) }}'+[[scope.row.id]]">
                                                        复制商品
                                                    </a>
                                                    &nbsp;&nbsp;
                                                    <a :href="'{{ yzWebFullUrl('goods.goods.edit', array('id' => '')) }}'+[[scope.row.id]]">
                                                        编辑
                                                    </a>&nbsp;&nbsp;
                                                    <a @click="delOne(scope.row.id)">
                                                        删除
                                                    </a>&nbsp;&nbsp;
                                                    <a @click="copyList(scope.row.id)">
                                                        复制链接
                                                    </a>
                                                    <div>
                                                        <input v-model="scope.row.link" :ref="'list'+scope.row.id"
                                                               style="position:absolute;opacity:0;height:1px;"/>
                                                    </div>
                                                </div>
                                                <div>
                                                    <el-checkbox border size="mini" v-model.number="scope.row.is_new"
                                                                 :true-label="1" :false-label="0"
                                                                 @change="setProperty(scope.row.id,scope.$index,'is_new')">
                                                        新品
                                                    </el-checkbox>
                                                    <el-checkbox border size="mini" v-model.number="scope.row.is_hot"
                                                                 :true-label="1" :false-label="0"
                                                                 @change="setProperty(scope.row.id,scope.$index,'is_hot')">
                                                        热卖
                                                    </el-checkbox>
                                                    <el-checkbox border size="mini"
                                                                 v-model.number="scope.row.is_recommand" :true-label="1"
                                                                 :false-label="0"
                                                                 @change="setProperty(scope.row.id,scope.$index,'is_recommand')">
                                                        推荐
                                                    </el-checkbox>
                                                    <el-checkbox border size="mini"
                                                                 v-model.number="scope.row.is_discount" :true-label="1"
                                                                 :false-label="0"
                                                                 @change="setProperty(scope.row.id,scope.$index,'is_discount')">
                                                        促销
                                                    </el-checkbox>
                                                </div>

                                            </template>
                                        </el-table-column>
                                    </el-table>
                                    <!-- 表格end -->
                                </template>

                            </div>
                        </div>
                    </div>
                    <!-- 分页 -->
                    <div class="vue-page" v-show="total>1">
                        <el-row>
                            <el-col align="right">
                                <el-pagination layout="prev, pager, next,jumper" @current-change="search" :total="total"
                                               :page-size="per_size" :current-page="current_page" background
                                               v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </div>
                </div>

            </template>

        </div>
    </div>
    <script src="{{resource_get('static/js/qrcode.min.js')}}"></script>
    <script>
        var app = new Vue({
            el: "#app",
            delimiters: ['[[', ']]'],
            data() {
                return {
                    id: "",
                    img: "",//二维码
                    catlevel: 0,//是否显示三级分类
                    is_all_choose: 0,//是否全选
                    goods_list: [],//商品列表
                    change_title: "",//修改标题弹框赋值
                    change_price: "",//修改价格弹框赋值
                    change_stock: "",//修改库存弹框赋值
                    change_sort: "",//修改排序弹框赋值
                    all_loading: false,
                    status_list: [
                        {id: '', name: '全部状态'},
                        {id: 0, name: '下架'},
                        {id: 1, name: '上架'},
                    ],
                    sell_stock_list: [
                        {id: '', name: '全部'},
                        {id: 0, name: '售罄'},
                        {id: 1, name: '出售中'},
                    ],
                    brands_list: [],//品牌名称
                    category_list: [],
                    category_list_v2: [],
                    category_list_v3: [],
                    search_form: {
                        id_v1: '',
                        id_v2: '',
                        id_v3: ''
                    },
                    form: {},
                    level_list: [],

                    loading: false,
                    table_loading: false,
                    rules: {},
                    //分页
                    total: 0,
                    per_size: 0,
                    current_page: 0,
                    rules: {},
                }
            },
            created() {
                //this.getData();
                let that = this;
                document.onkeydown = function(){
                    if(window.event.keyCode == 13)
                        that.search(1);
                }

            },
            mounted() {
                let data = {!! $data !!};
                this.setData(data);
            },
            methods: {
                setData(data) {
                    this.goods_list = data.list.data;
                    let arr = [];
                    this.goods_list.forEach((item, index) => {
                        item.title = this.escapeHTML(item.title)
                        arr.push(Object.assign({}, item, {is_choose: 0}))//是否选中
                    });
                    this.goods_list = arr;
                    this.total = data.list.total;
                    this.current_page = data.list.current_page;
                    this.per_size = data.list.per_page;
                    this.brands_list = data.brands;
                    this.category_list = data.catetory_menus.ids;
                    this.catlevel = data.catetory_menus.catlevel;
                    //console.log(this.goods_list);
                },
                getData() {
                    var that = this;
                    that.table_loading = true;
                    that.$http.post("{!! yzWebFullUrl('goods.goods.goods-list') !!}").then(response => {
                        //console.log(response);
                        if (response.data.result == 1) {
                            this.setData(response.data.data);

                        } else {
                            that.$message.error(response.data.msg);
                        }
                        that.table_loading = false;
                    }), function (res) {
                        //console.log(res);
                        that.table_loading = false;
                    };
                },
                // 一级分类改变
                changeV1() {
                    this.search_form.id_v2 = "";
                    this.search_form.id_v3 = "";
                    this.category_list_v2 = [];
                    this.category_list_v3 = [];
                    this.category_list.find(item => {
                        if (item.id == this.search_form.id_v1) {
                            this.category_list_v2 = item.childrens;
                        }
                    });
                },
                // 二级分类改变
                changeV2() {
                    this.search_form.id_v3 = "";
                    this.category_list_v3 = [];
                    if (this.catlevel == 3) {
                        this.category_list_v2.find(item => {
                            if (item.id == this.search_form.id_v2) {
                                this.category_list_v3 = item.childrens;
                            }
                        })
                    }
                },
                // 搜索、分页
                search(page) {
                    var that = this;
                    console.log(that.search_form)
                    // 商品类型
                    let product_attr = [];
                    if (that.search_form.is_new == 1) {
                        product_attr.push('is_new')
                    }
                    if (that.search_form.is_hot == 1) {
                        product_attr.push('is_hot')
                    }
                    if (that.search_form.is_recommand == 1) {
                        product_attr.push('is_recommand')
                    }
                    if (that.search_form.is_discount == 1) {
                        product_attr.push('is_discount')
                    }
                    let json = {
                        page: page,
                        search: {
                            keyword: that.search_form.keyword,
                            status: that.search_form.status,
                            sell_stock: that.search_form.sell_stock,
                            brand_id: that.search_form.brand_id,
                            min_price: that.search_form.min_price,
                            max_price: that.search_form.max_price,
                            product_attr: product_attr,//商品类型
                        },
                        category: {
                            parentid: that.search_form.id_v1,
                            childid: that.search_form.id_v2,
                            thirdid: that.search_form.id_v3,
                        }
                    };
                    that.table_loading = true;
                    that.$http.post("{!! yzWebFullUrl('goods.goods.goods-search') !!}", json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            let arr = [];
                            that.goods_list = response.data.data.data;
                            that.goods_list.forEach((item, index) => {
                                item.title = that.escapeHTML(item.title)
                                arr.push(Object.assign({}, item, {is_choose: 0}))//是否选中
                            });
                            that.goods_list = arr;
                            that.total = response.data.data.total;
                            that.current_page = response.data.data.current_page;
                            that.per_size = response.data.data.per_page;
                        } else {
                            that.$message.error(response.data.msg);
                        }
                        that.table_loading = false;
                    }), function (res) {
                        console.log(res);
                        that.table_loading = false;
                    };
                },

                qrcodeScan(url) {//生成二维码
                    let qrcode = new QRCode('qrcode', {
                        width: 100,  // 二维码宽度
                        height: 100, // 二维码高度
                        render: 'image',
                        text: url
                    });
                    var data = $("canvas")[$("canvas").length - 1].toDataURL().replace("image/png", "image/octet-stream;");
                    console.log(data)
                    this.img = data;
                },
                // 活动二维码
                listCode(index) {
                    this.qrcodeScan(this.goods_list[index].link);
                },
                // 复制活动链接
                copyList(index) {
                    that = this;
                    let Url = that.$refs['list' + index];
                    console.log(Url)
                    Url.select(); // 选择对象
                    document.execCommand("Copy", false);
                    that.$message({message: "复制成功！", type: "success"});
                },
                // 单个选择
                oneChange(item) {
                    let that = this;
                    let is_all = 0;
                    that.goods_list.some((item, index) => {
                        if (item.is_choose == 1) {
                            is_all = 1;
                        } else {
                            is_all = 0;
                            return true;
                        }
                    })
                    that.is_all_choose = is_all;
                },
                // 全选
                allChoose() {
                    let that = this;
                    let status = 0;
                    if (that.is_all_choose == 1) {
                        status = 1;
                    } else {
                        status = 0;
                    }
                    that.goods_list.forEach((item, index) => {
                        item.is_choose = status;
                    })
                },
                // 上架、下架
                putAway(id, index) {
                    var that = this;
                    that.table_loading = true;
                    let data = that.goods_list[index].status;
                    let json = {id: id, type: 'status', data: data};
                    that.$http.post("{!! yzWebFullUrl('goods.goods.setPutaway') !!}", json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            that.$message.success('操作成功！');
                        } else {
                            that.$message.error(response.data.msg);
                            that.goods_list[index].is_choose == 1 ? 0 : 1;
                        }
                        that.table_loading = false;
                    }), function (res) {
                        console.log(res);
                        that.table_loading = false;
                    };
                },
                // 批量上架、下架
                batchPutAway(data) {
                    var that = this;
                    that.table_loading = true;
                    let ids = [];
                    that.goods_list.forEach((item, index) => {
                        if (item.is_choose == 1) {
                            ids.push(item.id);
                        }
                    })
                    let json = {data: data, ids: ids}
                    that.$http.post("{!! yzWebFullUrl('goods.goods.batchSetProperty') !!}", json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            that.$message.success('操作成功！');
                            that.is_all_choose = 0;
                            that.search(1);
                        } else {
                            that.$message.error(response.data.msg);
                        }
                        that.table_loading = false;
                    }), function (res) {
                        console.log(res);
                        that.table_loading = false;
                    };
                },
                // 单个删除
                delOne(id) {
                    var that = this;
                    that.$confirm('确定删除吗', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        that.table_loading = true;
                        that.$http.post("{!! yzWebFullUrl('goods.goods.destroy') !!}", {id: id}).then(response => {
                            console.log(response);
                            if (response.data.result == 1) {
                                that.$message.success("删除成功！");
                                that.search(1);
                            } else {
                                that.$message.error(response.data);
                            }
                            that.table_loading = false;
                        }), function (res) {
                            console.log(res);
                            that.table_loading = false;
                        };
                    }).catch(() => {
                        this.$message({type: 'info', message: '已取消修改'});
                    });
                },
                // 批量删除
                batchDestroy() {
                    var that = this;
                    that.$confirm('确定删除吗', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        that.table_loading = true;
                        let ids = [];
                        that.goods_list.forEach((item, index) => {
                            if (item.is_choose == 1) {
                                ids.push(item.id);
                            }
                        })
                        let json = {ids: ids}
                        that.$http.post("{!! yzWebFullUrl('goods.goods.batchDestroy') !!}", json).then(response => {
                            console.log(response);
                            if (response.data.result == 1) {
                                that.$message.success('操作成功！');
                                that.is_all_choose = 0;
                                that.search(1);
                            } else {
                                that.$message.error(response.data.msg);
                            }
                            that.table_loading = false;
                        }), function (res) {
                            console.log(res);
                            that.table_loading = false;
                        };
                    }).catch(() => {
                        this.$message({type: 'info', message: '已取消修改'});
                    });
                },
                // 新品、热卖、推荐、促销、
                setProperty(id, index, type) {
                    var that = this;
                    that.table_loading = true;
                    console.log(that.goods_list[index][type])
                    let data = that.goods_list[index][type];
                    let json = {id: id, type: type, data: data};
                    that.$http.post("{!! yzWebFullUrl('goods.goods.setProperty') !!}", json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            that.$message.success('操作成功！');
                        } else {
                            that.$message.error(response.data.msg);
                            that.goods_list[index][type] == 1 ? 0 : 1;
                        }
                        that.table_loading = false;
                    }), function (res) {
                        console.log(res);
                        that.table_loading = false;
                    };
                },
                // 编辑商品标题
                editTitle(index, type) {
                    let that = this;
                    if (type == 'title') {
                        that.change_title = "";
                        that.change_title = that.goods_list[index].title;
                    }
                    if (type == 'price') {
                        if (that.goods_list[index].has_option == 1) {
                            that.$message.error('多规格不支持快速修改');
                            return false;
                        }
                        that.change_price = "";
                        that.change_price = that.goods_list[index].price;
                    }
                    if (type == 'stock') {
                        if (that.goods_list[index].has_option == 1) {
                            that.$message.error('多规格不支持快速修改');
                            return false;
                        }
                        that.change_stock = "";
                        that.change_stock = that.goods_list[index].stock;
                    }
                    if (type == 'sort') {
                        that.change_sort = "";
                        that.change_sort = that.goods_list[index].display_order;
                    }
                },
                // 确认修改标题、价格、库存
                confirmChange(id, type) {
                    let that = this;
                    let value = '';
                    if (type == 'title') {
                        value = that.change_title;
                        if (that.change_title == '') {
                            that.$message.error('标题不能为空');
                            return false;
                        }
                    }
                    if (type == 'price') {
                        value = that.change_price;
                        if (!(/^\d+(\.\d+)?$/.test(that.change_price))) {
                            that.$message.error('请输入正确价格');
                            return false;
                        }
                    }
                    if (type == 'stock') {
                        value = that.change_stock;
                        if (!(/^\d+$/.test(that.change_stock))) {
                            that.$message.error('请输入正确数字');
                            return false;
                        }
                    }
                    let json = {
                        id: id,
                        type: type,
                        value: value,
                    };
                    that.table_loading = true;
                    that.$http.post("{!! yzWebFullUrl('goods.goods.change') !!}", json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            that.$message.success('操作成功！');
                            if (document.all) {
                                document.getElementById('app').click();
                            } else {// 其它浏览器
                                var e = document.createEvent('MouseEvents')
                                e.initEvent('click', true, true)
                                document.getElementById('app').dispatchEvent(e)
                            }
                            that.search(1);
                        } else {
                            that.$message.error(response.data.msg);
                        }
                        that.table_loading = false;
                    }), function (res) {
                        console.log(res);
                        that.table_loading = false;
                    };
                },
                // 确认修改排序
                confirmChangeSort(id) {
                    let that = this;
                    if (!(/^\d+$/.test(that.change_sort))) {
                        that.$message.error('请输入正确数字');
                        return false;
                    }
                    that.table_loading = true;
                    let json = {id: id, value: that.change_sort};
                    that.$http.post("{!! yzWebFullUrl('goods.goods.displayorder') !!}", json).then(response => {
                        console.log(response);
                        if (response.data.result == 1) {
                            that.$message.success('操作成功！');
                            // that.$refs.search_form.click();
                            if (document.all) {
                                document.getElementById('app').click();
                            } else {// 其它浏览器
                                var e = document.createEvent('MouseEvents')
                                e.initEvent('click', true, true)
                                document.getElementById('app').dispatchEvent(e)
                            }
                            that.search(1);
                        } else {
                            that.$message.error(response.data.msg);
                        }
                        that.table_loading = false;
                    }), function (res) {
                        console.log(res);
                        that.table_loading = false;
                    };
                },
                // 字符转义
                escapeHTML(a) {
                    a = "" + a;
                    return a.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, "\"").replace(/&apos;/g, "'");
                    ;
                },
            },
        })

    </script>
@endsection
