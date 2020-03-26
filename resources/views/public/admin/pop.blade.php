<script>
Vue.component('pop', {
    props: ["show"],
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
            {"name":"商城首页","href":"{{yzAppFullUrl('home')}}"},
            {"name":"分类导航","href":"{{ yzAppFullUrl('category') }}"},
          ],
          member:[
            {"name":"会员中心","href":"{{ yzAppFullUrl('member') }}"},
            {"name":"我的订单","href":"{{ yzAppFullUrl('member/orderList/0')}}"},
            {"name":"我的购物车","href":"{{ yzAppFullUrl('cart') }}"},
            {"name":"我的收藏","href":"{{ yzAppFullUrl('member/collection') }}"},
            {"name":"我的足迹","href":"{{ yzAppFullUrl('member/footprint') }}"},
            {"name":"会员充值","href":"{{ yzAppFullUrl('member/balance') }}"},
            {"name":"余额明细","href":"{{ yzAppFullUrl('member/detailed') }}"},
            {"name":"余额提现","href":"{{ yzAppFullUrl('member/balance') }}"},
            {"name":"我的收货地址","href":"{{ yzAppFullUrl('member/address') }}"},
          ],
          webapp:[
            {"name":"会员信息","href":"{{ yzAppFullUrl('member/info') }}"},
            {"name":"修改手机","href":"{{ yzAppFullUrl('member/editmobile') }}"},
            {"name":"余额","href":"{{ yzAppFullUrl('member/balance') }}"},
            {"name":"余额明细","href":"{{ yzAppFullUrl('member/detailed') }}"},
            {"name":"积分","href":"{{ yzAppFullUrl('member/integral_v2') }}"},
            {"name":"收入","href":"{{ yzAppFullUrl('member/income') }}"},
            {"name":"收入提现","href":"{{ yzAppFullUrl('member/withdrawal') }}"},
            {"name":"收入明细","href":"{{ yzAppFullUrl('member/incomedetails') }}"},
            {"name":"收入明细详情","href":"{{ yzAppFullUrl('member/member_income_incomedetails_info') }}"},
            {"name":"提现记录","href":"{{ yzAppFullUrl('member/presentationRecord') }}"},
            {"name":"收货地址","href":"{{ yzAppFullUrl('member/address') }}"},
            {"name":"添加收货地址","href":"{{ yzAppFullUrl('member/appendAddress') }}"},
            {"name":"未体现分销佣金","href":"{{ yzAppFullUrl('extension/notPresent') }}"},
            {"name":"我的足记","href":"{{ yzAppFullUrl('member/footprint') }}"},
            {"name":"我的收藏","href":"{{ yzAppFullUrl('member/collection') }}"},
            {"name":"我的关系","href":"{{ yzAppFullUrl('member/myrelationship') }}"},
            {"name":"我的评价","href":"{{ yzAppFullUrl('member/myEvaluation') }}"},
            {"name":"评价详情","href":"{{ yzAppFullUrl('CommentDetails/2476/303/0') }}"},
            {"name":"我的推广","href":"{{ yzAppFullUrl('member/extension') }}"},
            {"name":"分销商","href":"{{ yzAppFullUrl('extension/distribution') }}"},
            {"name":"预计佣金","href":"{{ yzAppFullUrl('extension/commission') }}"},
            {"name":"未结算佣金","href":"{{ yzAppFullUrl('extension/unsettled') }}"},
            {"name":"已结算佣金","href":"{{ yzAppFullUrl('extension/alreadySettled') }}"},
            {"name":"未提现佣金","href":"{{ yzAppFullUrl('extension/notPresent') }}"},
            {"name":"已提现佣金","href":"{{ yzAppFullUrl('extension/present') }}"},
            {"name":"分销订单","href":"{{ yzAppFullUrl('extension/distributionOrder') }}"},
            {"name":"售后列表","href":"{{ yzAppFullUrl('member/aftersaleslist') }}"},
            {"name":"优惠券","href":"{{ yzAppFullUrl('coupon/coupon_index') }}"},
            {"name":"领券中心","href":"{{ yzAppFullUrl('coupon/coupon_store') }}"},
            {"name":"搜索","href":"{{ yzAppFullUrl('search') }}"},
            {"name":"登录","href":"{{ yzAppFullUrl('login') }}"},
            {"name":"注册","href":"{{ yzAppFullUrl('register') }}"},
            {"name":"分类","href":"{{ yzAppFullUrl('category') }}"},
            {"name":"品牌","href":"{{ yzAppFullUrl('brand') }}"},
            {"name":"购物车","href":"{{ yzAppFullUrl('cart') }}"},
            {"name":"填写订单","href":"{{ yzAppFullUrl('goodsorder') }}"},
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
          let confirm=this.show;
          confirm=false;
          this.$emit("add",child,confirm);
          this.inniData();
        },
        closeShow(){
         let item=this.show;
         item=false;
         this.$emit('replace', item);
         this.inniData();
        },
        // check(){  
        //   var Expression=/http?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
        //   var objExp=new RegExp(Expression);
        //   if(objExp.test(this.url)==true){
        //     let child=this.url;
        //     let confirm=this.show;
        //     confirm=false;
        //     this.$emit("add",child,confirm);
        //     this.url=''
        //   }
        //   else{
        //     this.$message('请输入正确的网址');
        //   }
        // },
        check(){
            let child=this.url;
            let confirm=this.show;
            confirm=false;
            this.$emit("add",child,confirm);
            this.url=''
        },
        search() {
                let json={
                  kw:this.keyword
                }
                this.$http.post('{!! yzWebFullUrl('goods.goods.getMyLinkGoods') !!}',json).then(function (response){
                    this.Goods=response.body;

                },function (response) {
                    console.log(response);
                }
                );
        },
        getLevel() {
                this.tabID=2;
               
                this.$http.get('{!! yzWebFullUrl('link.link.categoryLink') !!}').then(function (response){
                    console.log(response)
                    this.tree=response.data;
                },function (response) {
                    console.log(response);
                }
                );
        },
        getBrands() {
                this.tabID=3;
                this.$http.get('{!! yzWebFullUrl('link.link.brandLink') !!}').then(function (response){
                    this.Brands=response.data;
                },function (response) {
                    console.log(response);
                }
                );
        },
    },
  template: `
  <div class="dialog" v-if="show">
    <div class="dialog-cover" v-if="show" @click="closeShow"></div>
    <div class="dialog-content">
      <div class="close" @click="closeShow  ">X</div>
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
          <h4><i class="fa fa-folder-open-o"></i>webapp链接</h4>
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
            </div>
            <div style="clear: both;"></div>
         </div>
      </div>
      <div class="classification-content" v-show="tabID==2">
        <div class="tree" v-for="(item,index,key) in tree" >
          <div class="first-tree">
            <span class="name">[[item.name]]</span>
            <span class="link-href" @click="addHref(item.url)" :href="item.url">选择</span>
          </div>
          <div v-for="(list,index,key) in item.has_many_children" v-if="item.has_many_children.length>0" class="tree-two">
            <div class="second-tree">
              <div class="name">
                <span class="line"></span>
                <span class="text">[[list.name]]</span>
              </div>
              <span class="link-href" @click="addHref(list.url)" :href="list.url">选择</span>
            </div>
            <div v-for="(obj,index,key) in list.has_many_children" v-if="list.has_many_children.length>0">
                <div class="third-tree">
                  <div class="name">
                    <span class="line"></span>
                    <span class="text">[[obj.name]]</span>
                  </div>
                  <span class="link-href" @click="addHref(obj.url)" :href="obj.url">选择</span>
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
          <textarea class="input" placeholder="请以http://开头" v-model="url"></textarea>
          <div class="insert" @click="check">插入</div>
        </div>
      </div>
    </div>
  </div>
  `
});
</script>
