<script>
Vue.component('program', {
    props: ["pro"],
    delimiters: ['[[', ']]'],
    data(){
        return{
          tabID:0,
          tree:[],
          Brands:[],
          url:'',
          keyword:'',
          Goods:[],
          store:[
            {"name":"商城首页","href":"/packageD/index/index"},
            {"name":"分类导航","href":"/packageD/category_v2/category_v2"},
            {"name":"全部商品","href":"/packageC/member/searchAll/searchAll"},
            {"name":"门店聚合页面","href":"/packageC/o2o/o2oHome/o2oHome"},
          ],
          member:[
            {"name":"会员中心","href":"/packageD/member/index_v2/index_v2"},
            {"name":"我的订单","href":"/packageA/member/myOrder_v2/myOrder_v2"},
            {"name":"我的购物车","href":"/packageD/buy/cart_v2/cart_v2"},
            {"name":"我的收藏","href":"/packageD/member/collection/collection"},
            {"name":"我的足迹","href":"/packageD/member/footprint/footprint"},
            {"name":"会员充值","href":"/packageA/member/balance/balance/balance"},
            {"name":"收货地址","href":"/packageD/member/addressList/addressList"},
          ],
          webapp:[
            {"name":"会员信息","href":"/packageA/member/info/info"},
            {"name":"修改手机","href":"/packageA/member/editmobile/editmobile"},
            {"name":"会员余额","href":"/packageD/member/balance/balance"},
            {"name":"余额明细","href":"/packageA/member/balance/detailed/detailed"},
            {"name":"会员积分","href":"/packageB/member/integral/integral"},
            {"name":"我的收入","href":"/packageB/member/income/income/income"},
            {"name":"收入提现","href":"/packageA/member/withdrawal/withdrawal"},
            {"name":"收入明细","href":"/packageA/member/extension/incomedetails/incomedetails"},
            {"name":"收入明细详情","href":"/packageA/member/income_details_info/income_details_info"},
            {"name":"提现记录","href":"/packageA/member/presentationRecord/presentationRecord"},
            {"name":"收货地址","href":"/packageD/member/addressList/addressList"},
            {"name":"添加收货地址","href":"/packageD/member/addressAdd_v2/addressAdd_v2"},
            {"name":"未提现分销佣金","href":"/packageA/member/extension/notPresent/notPresent"},
            {"name":"我的足记","href":"/packageD/member/footprint/footprint"},
            {"name":"我的收藏","href":"/packageD/member/collection/collection"},
            {"name":"我的关系","href":"/packageD/member/myRelationship/myRelationship"},
            {"name":"我的评价","href":"/packageD/member/myRelationship/myRelationship"},
            {"name":"我的推广","href":"/packageD/member/extension/extension"},
            {"name":"分销商","href":"/packageA/member/distribution/distribution"},
            {"name":"预计佣金","href":"/packageA/member/extension/commission/commission"},
            {"name":"未结算佣金","href":"/packageA/member/extension/unsettled/unsettled"},
            {"name":"已结算佣金","href":"/packageA/member/extension/alreadySettled/alreadySettled"},
            {"name":"未提现佣金","href":"/packageA/member/extension/notPresent/notPresent"},
            {"name":"已提现佣金","href":"/packageA/member/extension/present/present"},
            {"name":"分销订单","href":"/packageA/member/extension/present/present"},
            {"name":"售后列表","href":"/packageD/member/myOrder/Aftersaleslist/Aftersaleslist"},
            {"name":"优惠券","href":"/packageA/member/coupon_v2/coupon_v2"},
            {"name":"领券中心","href":"/packageD/coupon/coupon_store"},
            {"name":"搜索","href":"/packageB/member/category/search_v2/search_v2"},
            {"name":"分类","href":"/packageD/category_v2/category_v2"},
            {"name":"品牌","href":"/packageB/member/category/brand_v2/brand_v2"},
            {"name":"购物车","href":"/packageD/buy/cart_v2/cart_v2"},
            {"name":"填写订单","href":"/packageC/o2o/o2oHome/o2oHome"},
            {"name":"音频文章","href":"packageA/member/course/VoiceList/VoiceL"},
          ]
        }
    },
    watch:{
        
    },
    mounted: function(){

    },
    methods:{
        inniData(){
          this.tabID=0;
          this.url='';
          this.keyword='';
          this.Goods=[];
        },
        addHref(item){
          let child=item;
          let confirm=this.Show;
          confirm=false;
          this.$emit("addpro",child,confirm);
          this.inniData();
        },
        closeShow(){
         let item=this.Show;
         item=false;
         this.$emit('replacepro', item);
         this.inniData();
        },
        check(){
          // var Expression=/http?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
          // var objExp=new RegExp(Expression);
          // if(objExp.test(this.url)==true){
            let child=this.url;
            let confirm=this.pro;
            confirm=false;
            this.$emit("addpro",child,confirm);
            this.url=''
          // }
          // else{
          //   this.$message('请输入正确的网址');
          // }
        },
        search() {
                let json={
                  kw:this.keyword
                }
                this.$http.post('{!! yzWebFullUrl('goods.goods.getSmallMyLinkGoods') !!}',json).then(function (response){
                    this.Goods=response.body;

                },function (response) {
                    console.log(response);
                }
                );
        },
        getLevel() {
                this.tabID=2;
               
                this.$http.get('{!! yzWebFullUrl('link.link.categoryLink') !!}').then(function (response){
                    
                    this.tree=response.data;
                },function (response) {
                    console.log(response);
                }
                );
        },
        getBrands() {
                this.tabID=3;
                this.$http.get('{!! yzWebFullUrl('link.link.smallProceduresBrandLink') !!}').then(function (response){
                    this.Brands=response.data;
                },function (response) {
                    console.log(response);
                }
                );
        },
    },
  template: `
  <div class="dialog" v-if="pro">
    <div class="dialog-cover" v-if="pro" @click="closeShow"></div>
    <div class="dialog-content">
      <div class="close" @click="closeShow">X</div>
      <div class="dialog-header">
        <ul class="tablist">
          <li @click="tabID=0"><a :class="{popTab:tabID==0}">系统页面</a></li>
          <li @click="tabID=1"><a :class="{popTab:tabID==1}">商品链接</a></li>
          <li @click="getLevel"><a :class="{popTab:tabID==2}">商品分类</a></li>
          <li @click="getBrands"><a :class="{popTab:tabID==3}">商品品牌</a></li>
          <li @click="tabID=4"><a :class="{popTab:tabID==4}">自定义链接</a></li>
        </ul>
      </div>
      <div class="link-content" v-show="tabID==0">
        <div class="page">
          <h4><i class="fa fa-folder-open-o"></i>商城页面链接</h4>
          <span class="link" v-for="(item,index,key) in store" @click="addHref(item.href)" :href="item.href">[[item.name]]</span>
        </div>
        <div class="page">
          <h4><i class="fa fa-folder-open-o"></i>会员中心链接</h4>
          <span class="link" v-for="(item,index,key) in member" @click="addHref(item.href)" :href="item.href">[[item.name]]</span>
        </div>
        <div class="page">
          <h4><i class="fa fa-folder-open-o"></i>其他链接</h4>
          <span class="link" v-for="(item,index,key) in webapp" @click="addHref(item.href)" :href="item.href">[[item.name]]</span>
        </div>
      </div>
      <div class="search-content" v-show="tabID==1">
         <input type="text" placeholder="请输入商品名称进行搜索 (多规格商品不支持一键下单)" class="sou" v-model="keyword">
         <span class="sou-btn" @click="search">搜索</span>
         <div class="search-goods" style="max-height: 400px;overflow: scroll;overflow-x: hidden;">
            <div class="goods" v-for="(item,index,key) in Goods">
              <div class="info">
                <div class="img">
                   <img :src="item.thumb">
                </div>
                <div class="right-content">
                  <div class="top"><span class="text">[[item.title]]</span><span :href="item.url" class="href" @click="addHref(item.url)">详情链接</span></div>
                  <div class="bottom"><span>原价:￥[[item.market_price]]</span><span>现价￥[[item.price]]</span></div>
                </div>
                <div style="clear: both;"></div>
              </div>
                <div style="clear: both;"></div>
            </div> 
         </div>
      </div>
      <div class="classification-content" v-show="tabID==2">
        <div class="tree" v-for="(item,index,key) in tree" >
          <div class="first-tree">
            <span class="name">[[item.name]]</span>
            <span class="link-href" @click="addHref(item.procedures_url)" :href="item.procedures_url">选择</span>
          </div>
          <div v-for="(list,index,key) in item.has_many_children" v-if="item.has_many_children.length>0" class="tree-two">
            <div class="second-tree">
              <div class="name">
                <span class="line"></span>
                <span class="text">[[list.name]]</span>
              </div>
              <span class="link-href" @click="addHref(list.procedures_url)" :href="list.procedures_url">选择</span>
            </div>
            <div v-for="(obj,index,key) in list.has_many_children" v-if="list.has_many_children.length>0">
                <div class="third-tree">
                  <div class="name">
                    <span class="line"></span>
                    <span class="text">[[obj.name]]</span>
                  </div>
                  <span class="link-href" @click="addHref(obj.procedures_url)" :href="obj.procedures_url">选择</span>
                </div>                 
            </div>
          </div>
        </div>
      </div>
      <div class="brands-content" v-show="tabID==3">
        <div class="link" v-for="(item,index,key) in Brands">
        <span class="name">[[item.name]]</span>
        <span class="link-href" @click="addHref(item.url)" :href="item.url">选择</span>
        </div>
      </div>
      <div class="customize-content" v-show="tabID==4">
        <span class="text">链接地址</span>
        <div class="right">
          <textarea class="input" placeholder="" v-model="url"></textarea>
          <div class="insert" @click="check">插入</div>
        </div>
      </div>
    </div>
  </div>
  `
});
</script>