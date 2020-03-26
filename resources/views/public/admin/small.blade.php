<!-- mylink start -->
<style>
    body {
        background: #eee;
    }

    .topmenu {
        background: #ddd;
    }

    .fart-editor-content .menu, .fart-editor-menu nav, .fart-editor-content .con2 .con .itembox, .fart-preview .title, .adddiv, .fart-editor-menu .savebtn {
        moz-user-select: -moz-none;
        -moz-user-select: none;
        -o-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .loading {
        background: #ddd;
        border: 1px solid #ccc;
        color: #999;
    }

    .mylink-con {
        height: 300px;
        overflow-y: auto;
    }

    .mylink-line {
        height: 36px;
        border-bottom: 1px dashed #eee;
        line-height: 36px;
        color: #999;
    }

    .mylink-sub {
        height: 36px;
        width: 50px;
        padding-right: 15px;
        float: right;
        text-align: right;
    }

    .mylink-con .good {
        height: 70px;
        width: 330px;
        padding: 5px;
        margin: 5px 2px 0px;
        background: #f5f5f5;
        float: left;
    }

    .mylink-con .good .img {
        height: 60px;
        width: 60px;
        background: #eee;
        float: left;
    }

    .mylink-con .good .img img {
        height: 100%;
        width: 100%;
        border: 0px;
        display: block;
    }

    .mylink-con .good .choosebtn {
        height: 60px;
        width: 80px;
        float: right;
        line-height: 30px;
        text-align: right;
    }

    .mylink-con .good .info {
        height: 60px;
        word-break: break-all;
        padding-left: 70px;
        color: #999;
    }

    .mylink-con .good .info-title {
        height: 40px;
        line-height: 20px;
        overflow: hidden;
    }

    .mylink-con .good .info-price {
        height: 20px;
        line-height: 20px;
        font-size: 12px;
    }

    .fart-main ::-webkit-scrollbar {
        width: 6px;
    }

    .fart-main ::-webkit-scrollbar-track {
    }

    .fart-main ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
    }

    .fart-main ::-webkit-scrollbar-thumb:window-inactive {
        background: rgba(0, 0, 0, 0.1);
    }

    .fart-main ::-webkit-scrollbar-thumb:vertical:hover {
        background-color: rgba(0, 0, 0, 0.3);
    }

    .fart-main ::-webkit-scrollbar-thumb:vertical:active {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .edui-default .edui-editor-toolbarboxouter, .edui-default .edui-editor-toolbarbox {
        border: 0px;
        border-radius: 0px
    }

    .datetimepicker {
        margin: 0px;
    }

    section a, section a:hover {
        color: inherit;
    }

    .fart-main {
        height: auto;
        width: 1400px;
        overflow: hidden;
    }

    .fart-preview {
        height: 800px;
        width: 400px;
        float: left;
        background: #f1f1f1;
    }

    .fart-preview section {
        padding: 0px;
        margin: 0px;
    }

    .fart-preview .title {
        height: 50px;
        background: #00a8e8;
        color: #fff;
        text-align: center;
        line-height: 50px;
        font-size: 18px;
        cursor: default;
        display: none;
    }

    .fart-preview .top {
        height: 50px;
        background: #3366d7;
        background: #3e4144 url('./top_bg.png') center -3px no-repeat;
        overflow: hidden;
        cursor: default;
    }

    .fart-preview .top p {
        height: 20px;
        width: 260px;
        margin: auto;
        font-size: 16px;
        color: #fff;
        margin-top: 24px;
        text-align: center;
        line-height: 20px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        content: "...";
    }

    .fart-preview .main {
        height: 750px;
        overflow-y: auto;
    }

    .fart-rich-primary {
        min-height: 750px;
        padding: 20px 15px 15px;
        background: #fff;
        cursor: default;
    }

    .fart-rich-title {
        margin-bottom: 10px;
        line-height: 1.4;
        font-weight: 400;
        font-size: 24px;
    }

    .fart-rich-mate {
        margin-bottom: 18px;
        line-height: 20px;
        overflow: hidden;
    }

    .fart-rich-mate-text {
        margin-right: 8px;
        margin-bottom: 10px;
        font-size: 16px;
        color: #8c8c8c;
        float: left;
    }

    .fart-rich-mate .href {
        color: #607fa6;
    }

    .fart-rich-content {
        min-height: 577px;
        font-size: 16px;
    }

    .fart-rich-content img {
        max-width: 100%;
    }

    .fart-rich-tool {
        height: auto;
        padding-top: 15px;
        line-height: 32px;
        overflow: hidden;
    }

    .fart-rich-tool-text {
        margin-right: 10px;
        font-size: 16px;
        color: #8c8c8c;
        text-decoration: none;
        float: left;
    }

    .fart-rich-tool .link {
        color: #607fa6;
    }

    .fart-rich-tool .right {
        float: right;
    }

    .fart-rich-tool-like {
        height: 13px;
        width: 13px;
        margin-left: 8px;
        background: url('./like.png') 0 0 no-repeat;
        background-size: 100% auto;
        display: inline-block;
    }

    .fart-rich-sift {
        height: auto;
        background: #ddd;
        padding: 30px 15px 0px;
        display: none;
    }

    .fart-rich-sift-line {
        height: 21px;
        position: relative;
    }

    .fart-rich-sift-border {
        height: 0px;
        width: 100%;
        border-top: 1px dashed #eee;
        position: absolute;
        top: 10px;
        left: 0px;
        z-index: 1;
    }

    .fart-rich-sift-text {
        height: 21px;
        width: 100%;
        font-size: 14px;
        color: #999;
        line-height: 21px;
        text-align: center;
        font-size: 16px;
        z-index: 2;
        position: absolute;
        top: 0px;
        left: 0px;
    }

    .fart-rich-sift-text a {
        display: inline-block;
        padding: 0px 5px;
        background: #ddd;
        color: #999;
        height: 21px;
        max-width: 80%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        content: "...";
    }

    .fart-rich-sift-img {
        min-height: 10px;
        background: #fff;
        margin-top: 12px;
        padding: 6px;
    }

    .fart-rich-sift-img img {
        width: 100%;
        border: 0px;
        display: block;
    }

    .fart-rich-sift-more {
        line-height: 60px;
        font-size: 16px;
        color: #607fa6;
        text-align: center;
        height: 60px;
        margin: auto;
        max-width: 80%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        content: "...";
    }

    .fart-editor {
        height: 800px;
        width: 1000px;
        background: #f1f1f1;
        float: left;
        font-weight: 100;
    }

    .fart-editor-menu {
        height: 50px;
        background: #00a8e8;
    }

    .fart-editor-menu nav {
        height: 50px;
        width: 30%;
        text-align: center;
        line-height: 50px;
        font-size: 18px;
        color: #fff;
        float: left;
        cursor: pointer;
    }

    .fart-editor-menu .navon {
        background: #00b3f7;
    }

    .fart-editor-menu .savebtn {
        height: 50px;
        width: 10%;
        background: #6c9;
        float: left;
        line-height: 50px;
        text-align: center;
        font-size: 18px;
        color: #fff;
        cursor: pointer;
    }

    .fart-editor-content {
        height: 750px;
        background: #f1f1f1;
        display: none;
        overflow: hidden;
    }

    .fart-editor-content .menu {
        height: 40px;
        cursor: default;
    }

    .fart-editor-content .nav1 {
        height: 40px;
        width: 500px;
        background: #ffba75;
        font-size: 16px;
        color: #fff;
        line-height: 40px;
        text-align: center;
        float: left;
        position: relative;
    }

    .fart-editor-content .nav1 .trash {
        height: 24px;
        width: 24px;
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 20px;
        line-height: 24px;
        text-align: center;
        cursor: pointer;
    }

    .fart-editor-content .nav2 {
        height: 40px;
        width: 500px;
        background: #b4b4da;
        font-size: 16px;
        color: #fff;
        line-height: 40px;
        text-align: center;
        float: left;
        position: relative;
    }

    .fart-editor-content .nav2 .tip {
        height: 20px;
        width: 40px;
        position: absolute;
        right: 55px;
        top: 10px;
        font-size: 12px;
        color: #fff;
        line-height: 20px;
        text-align: center;
    }

    .fart-editor-content .nav2 .color {
        height: 20px;
        width: 40px;
        position: absolute;
        right: 15px;
        top: 10px;
        cursor: pointer;
        border: 0px;
        padding: 0px;
        outline: none;
    }

    .fart-editor-content .nav2 .color::-webkit-color-swatch-wrapper {
        border: 0px;
        padding: 0px;
    }

    .fart-editor-content .content {
        height: 710px;
    }

    .fart-editor-content .con1 {
        height: 710px;
        width: 500px;
        background: #f4f4f4;
        float: left;
    }

    .fart-editor-content .con2 {
        height: 710px;
        width: 500px;
        background: #f4f4f4;
        float: left;
    }

    .fart-editor-content .con2 .tab {
        height: 710px;
        width: 74px;
        background: #ccc;
        float: left;
    }

    .fart-editor-content .con2 .tab .nav {
        height: 42px;
        line-height: 42px;
        text-align: center;
        font-size: 16px;
        color: #fff;
        cursor: pointer;
    }

    .fart-editor-content .con2 .tab .navon {
        background: #aaa;
    }

    .fart-editor-content .con2 .con {
        height: 710px;
        width: 426px;
        float: left;
        display: none;
        overflow-y: auto;
        background: #fff;
    }

    .fart-editor-content .con2 .con img {
        max-width: 100%;
    }

    .fart-editor-content .con2 .con .itembox {
        border-bottom: 1px dashed #ddd;
        padding: 10px;
        cursor: pointer;
    }

    .fart-form {
        min-height: 500px;
        padding: 40px;
    }

    .fart-form input::-webkit-input-placeholder {
        color: #999;
    }

    .fart-form input {
        color: #333;
    }

    .fart-form .line {
        height: auto;
        overflow: hidden;
    }

    .fart-form .line2 {
        height: auto;
        width: 455px;
        float: left;
    }

    .fart-form .product {
        display: none;
    }

    .fart-form .product .advs {
        min-height: 10px;
        background: #eee;
        padding: 5px;
        margin-bottom: 15px;
        border: 2px dashed #ccc;
        border-radius: 5px;
        overflow: hidden;
    }

    .fart-form .product .advs .addbtn {
        height: 40px;
        border: 2px dashed #ccc;
        line-height: 40px;
        font-size: 18px;
        color: #bbb;
        text-align: center;
        cursor: pointer;
        margin: 5px;
        background: #fff;
    }

    .fart-form .product .adv {
        height: 100px;
        background: #fff;
        border: 1px solid #ddd;
        margin: 5px;
        padding: 5px;
        border-radius: 5px;
        position: relative;
    }

    .fart-form .product .adv .img {
        height: 88px;
        width: auto;
        min-width: 88px;
        max-width: 250px;
        background: #ccc;
        float: left;
        margin-right: 15px;
    }

    .fart-form .product .adv .img img {
        height: 100%;
        width: auto;
    }

    .fart-form .product .adv .info {
        height: 90px;
    }

    .fart-form .product .adv .del {
        height: 24px;
        width: 24px;
        background: rgba(0, 0, 0, 0.5);
        text-align: center;
        line-height: 24px;
        color: #fff;
        font-size: 18px;
        position: absolute;
        top: -10px;
        right: -10px;
        border-radius: 30px;
        cursor: pointer;
    }

    .page-header {
        height: 40px;
    }

    .mylink-app-nav {
        margin: 5px 0;
    }
</style>

<div id="modal-myApplink" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 720px;">
        <div class="modal-content">
            <div class="modal-header" style="padding: 5px;">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <ul class="nav nav-pills" role="tablist">
                    <li role="presentation" class="active" style="display: block;">
                        <a aria-controls="link_small_system" role="tab" data-toggle="tab" href="#link_small_system"
                           aria-expanded="true">
                            系统页面
                        </a>
                    </li>
                    <li role="presentation" style="display: block;">
                        <a aria-controls="link_small_goods" role="tab" data-toggle="tab" href="#link_small_goods"
                           aria-expanded="false">
                            商品链接
                        </a>
                    </li>
                    <li role="presentation" style="display: block;">
                        <a aria-controls="link_small_cate" role="tab" data-toggle="tab" href="#link_small_cate"
                           aria-expanded="false">
                            商品分类
                        </a>
                    </li>
                    <li role="presentation" style="display: block;">
                        <a aria-controls="link_small_brand" role="tab" data-toggle="tab" href="#link_small_brand"
                           aria-expanded="false">
                            商品品牌
                        </a>
                    </li>
                </ul>
            </div>
            <div class="modal-body tab-content">
                <div role="tabpanel" class="tab-pane link_small_system active" id="link_small_system">
                    <div class="mylink-con">

                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i>商城页面链接</h4>
                        </div>
                        <div id="fe-tab-link-li-11" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 11)" data-href="/pages/index/index">商城首页</div>
                        <div id="fe-tab-link-li-12" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 12)" data-href="/pages/category_v2/category_v2">分类导航</div>
                        <div id="fe-tab-link-li-13" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 13)" data-href="/packageC/member/searchAll/searchAll">全部商品</div>
                        <div id="fe-tab-link-li-14" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 14)" data-href="/packageC/o2o/o2oHome/o2oHome">门店聚合页面</div>

                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i>会员中心链接</h4>
                        </div>
                        <div id="fe-tab-link-li-21" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 21)" data-href="/pages/member/index_v2/index_v2">会员中心</div>
                        <div id="fe-tab-link-li-22" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 22)" data-href="/packageA/member/myOrder_v2/myOrder_v2">我的订单</div>
                        <div id="fe-tab-link-li-23" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 23)" data-href="/pages/buy/cart_v2/cart_v2">我的购物车</div>
                        <div id="fe-tab-link-li-24" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 24)" data-href="/pages/member/collection/collection">我的收藏</div>
                        <div id="fe-tab-link-li-25" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 25)" data-href="/pages/member/footprint/footprint">我的足迹</div>
                        <div id="fe-tab-link-li-26" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 26)" data-href="/packageA/member/balance/balance/balance">会员充值</div>
                        {{--<div id="fe-tab-link-li-27" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 27)" data-href="/packageA/member/balance/detailed/detailed">余额明细</div>--}}
                        {{--<div id="fe-tab-link-li-28" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 28)" data-href="/packageA/member/balance/balance/balance">余额提现</div>--}}
                        <div id="fe-tab-link-li-29" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 29)" data-href="/pages/member/addressList/addressList">收货地址</div>

                        <div class="page-header">
                            <h4><i class="fa fa-folder-open-o"></i>其他链接</h4>
                        </div>
                        <div id="fe-tab-link-li-34" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 34)" data-href="/packageA/member/info/info">会员信息</div>
                        <div id="fe-tab-link-li-35" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 35)" data-href="/packageA/member/editmobile/editmobile">修改手机</div>
                        <div id="fe-tab-link-li-36" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 36)" data-href="/pages/member/balance/balance">会员余额</div>
                        <div id="fe-tab-link-li-37" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 37)" data-href="/packageA/member/balance/detailed/detailed">余额明细</div>
                        <div id="fe-tab-link-li-40" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 40)" data-href="/packageB/member/integral/integral">会员积分</div>
                        <div id="fe-tab-link-li-41" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 41)" data-href="/packageB/member/income/income/income">我的收入</div>
                        <div id="fe-tab-link-li-44" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 44)" data-href="/packageA/member/withdrawal/withdrawal">收入提现</div>
                        <div id="fe-tab-link-li-45" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 45)" data-href="/packageA/member/extension/incomedetails/incomedetails">收入明细</div>
                        <div id="fe-tab-link-li-46" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 46)" data-href="/packageA/member/income_details_info/income_details_info">收入明细详情</div>
                        <div id="fe-tab-link-li-48" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 48)" data-href="/packageA/member/presentationRecord/presentationRecord">提现记录</div>
                        <div id="fe-tab-link-li-50" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 50)" data-href="/pages/member/addressList/addressList">收货地址</div>
                        <div id="fe-tab-link-li-52" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 52)" data-href="/pages/member/addressAdd_v2/addressAdd_v2">添加收货地址</div>
                        <div id="fe-tab-link-li-53" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 53)" data-href="/packageA/member/extension/notPresent/notPresent">未提现分销佣金</div>
                        <div id="fe-tab-link-li-54" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 54)" data-href="/pages/member/footprint/footprint">我的足迹</div>
                        <div id="fe-tab-link-li-55" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 55)" data-href="/pages/member/collection/collection">我的收藏</div>
                        <div id="fe-tab-link-li-56" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 56)" data-href="/pages/member/myRelationship/myRelationship">我的关系</div>
                        <div id="fe-tab-link-li-58" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 58)" data-href="/pages/member/myRelationship/myRelationship">我的评价</div>
                        {{--<div id="fe-tab-link-li-60" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 60)" data-href="">评价详情</div>--}}
                        <div id="fe-tab-link-li-61" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 61)" data-href="/pages/member/extension/extension">我的推广</div>
                        <div id="fe-tab-link-li-62" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 62)" data-href="/packageA/member/distribution/distribution">分销商</div>
                        <div id="fe-tab-link-li-63" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 63)" data-href="/packageA/member/extension/commission/commission">预计佣金</div>
                        <div id="fe-tab-link-li-65" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 65)" data-href="/packageA/member/extension/unsettled/unsettled">未结算佣金</div>
                        <div id="fe-tab-link-li-67" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 67)" data-href="/packageA/member/extension/alreadySettled/alreadySettled">已结算佣金</div>
                        <div id="fe-tab-link-li-69" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 69)" data-href="/packageA/member/extension/notPresent/notPresent">未提现佣金</div>
                        <div id="fe-tab-link-li-71" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 71)" data-href="/packageA/member/extension/present/present">已提现佣金</div>
                        <div id="fe-tab-link-li-73" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 73)" data-href="/packageA/member/extension/present/present">分销订单</div>
                        <div id="fe-tab-link-li-81" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 81)" data-href="/pages/member/myOrder/Aftersaleslist/Aftersaleslist">售后列表</div>
                        <div id="fe-tab-link-li-84" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 84)" data-href="/packageA/member/coupon_v2/coupon_v2">优惠券</div>
                        <div id="fe-tab-link-li-85" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 85)" data-href="/pages/coupon/coupon_store">领券中心</div>
                        <div id="fe-tab-link-li-89" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 89)" data-href="/packageB/member/category/search_v2/search_v2">搜索</div>
                        {{--<div id="fe-tab-link-li-90" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 90)" data-href="">登录</div>--}}
                        {{--<div id="fe-tab-link-li-91" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 91)" data-href="">注册</div>--}}
                        <div id="fe-tab-link-li-92" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 92)" data-href="/pages/category_v2/category_v2">分类</div>
                        <div id="fe-tab-link-li-94" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 94)" data-href="/packageB/member/category/brand_v2/brand_v2">品牌</div>
                        <div id="fe-tab-link-li-96" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 96)" data-href="/pages/buy/cart_v2/cart_v2">购物车</div>
                        <div id="fe-tab-link-li-99" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 99)" data-href="/packageC/o2o/o2oHome/o2oHome">填写订单</div>
                        <div id="fe-tab-link-li-101" class="btn btn-default mylink-app-nav" ng-click="chooseLink(1, 101)" data-href="packageA/member/course/VoiceList/VoiceL">音频文章</div>

                    </div>
                </div>

                <!-- 商品链接 start -->
                <div role="tabpanel" class="tab-pane link_small_goods" id="link_small_goods">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="select-app-good-kw" placeholder="请输入商品名称进行搜索 (多规格商品不支持一键下单)">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"  id="select-app-good-btn">
                                搜索
                            </button>
                        </span>
                    </div>
                    <div class="mylink-con" id="select-app-goods" style="height:266px;">
                        <div class="good" ng-repeat="good in searchGoods">
                            <div class="img">
                                <img ng-src="@{{good.thumb}}">
                            </div>
                            <div class="choosebtn">
                                <a href="javascript:;" id="@{{good.id}}" ng-click="chooseLink(1, good.id)" data-href="/pages/detail_v2/detail_v2?id=@{{ good.goods_id }}">
                                    详情链接
                                </a>
                                <br>
                            </div>
                            <div class="info">
                                <div class="info-title">@{{good.title}}</div>
                                <div class="info-price">原价:￥@{{good.market_price}} 现价￥@{{good.price}}</div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- 商品链接 edn -->

                <!-- 商品分类 start -->
                <div role="tabpanel" class="tab-pane link_small_cate" id="link_small_cate">
                    <div class="mylink-con">
                        <?php $category = \app\backend\modules\goods\models\Category::getAllCategory(); ?>
                        @foreach ($category as $goodcate_parent)
                            @if (empty($goodcate_parent['parent_id']))
                                <div class="mylink-line">
                                    {{ $goodcate_parent['name'] }}
                                    <div class="mylink-sub">
                                        <a href="javascript:;" id="category-{{ $goodcate_parent['id'] }}" class="mylink-app-nav" ng-click="chooseLink(1, 'category-{{ $goodcate_parent['id'] }}')" data-href="/packageB/member/category/catelist/catelist?id={!! $goodcate_parent['id'] !!}">选择</a>
                                    </div>
                                </div>
                                <!-- 二级分类 -->
                                @foreach ($category as $goodcate_chlid)
                                    @if ($goodcate_chlid['parent_id'] == $goodcate_parent['id'])
                                        <div class="mylink-line">
                                            <span style='height:10px; width: 10px; margin-left: 10px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                            {{ $goodcate_chlid['name'] }}
                                            <div class="mylink-sub">
                                                <a href="javascript:;" id="category-{{ $goodcate_chlid['id'] }}" class="mylink-app-nav" ng-click="chooseLink(1, 'category-{{ $goodcate_chlid['id'] }}')" data-href="/packageB/member/category/catelist/catelist?id={!! $goodcate_chlid['id'] !!}">选择</a>
                                            </div>
                                        </div>
                                        <!-- 三级分类 -->
                                        @foreach ($category as $goodcate_third)
                                            @if ($goodcate_third['parent_id'] == $goodcate_chlid['id'])
                                                <div class="mylink-line">
                                                    <span style='height:10px; width: 10px; margin-left: 30px; margin-right: 10px; display:inline-block; border-bottom: 1px dashed #ddd; border-left: 1px dashed #ddd;'></span>
                                                    {{ $goodcate_third['name'] }}
                                                    <div class="mylink-sub">
                                                        <a href="javascript:;" id="category-{{ $goodcate_third['id'] }}" class="mylink-app-nav" ng-click="chooseLink(1, 'category-{{ $goodcate_third['id'] }}')" data-href="/packageB/member/category/catelist/catelist?id={!! $goodcate_third['id'] !!}">选择</a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                        <!-- 三级分类 end -->
                                    @endif
                                @endforeach
                                <!-- 二级分类 end -->
                            @endif
                        @endforeach
                    </div>
                </div>
                <!-- 商品分类 end -->

                <!-- 商品品牌 start -->
                <div role="tabpanel" class="tab-pane link_small_brand" id="link_small_brand">
                    <div class="mylink-con">
                        <?php $brands = \app\common\models\Brand::getBrands()->select('id', 'name')->get(); ?>
                        @if($brands)
                            @foreach ($brands->toArray() as $brand)
                                <div class="mylink-line">
                                    {{ $brand['name'] }}
                                    <div class="mylink-sub">
                                        <a href="javascript:;" id="brand-{{ $brand['id'] }}" class="mylink-app-nav" ng-click="chooseLink(1, 'brand-{{ $brand['id'] }}')" data-href="/packageB/member/category/brandgoods/brandgoods?id={!! $brand['id'] !!}">
                                            选择
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <!-- 商品品牌 end -->

                {!! my_link_extra('content') !!}
            </div>
        </div>
    </div>
</div>

<!-- mylink end -->
<script language="javascript">
    require(['jquery'],function(){


        $(function() {
            $("#chkoption").click(function() {
                var obj = $(this);
                if (obj.get(0).checked) {
                    $("#tboption").show();
                    $(".trp").hide();
                }
                else {
                    $("#tboption").hide();
                    $(".trp").show();
                }
            });
        })

        $(document).on("click",".nav-app-link",function(){
            var id = $(this).data("id");
            if(id){
                $("#modal-myApplink").attr({"data-id":id});
                $("#modal-myApplink").modal();
            }
        });
        $(document).on("click",".mylink-app-nav",function(){
            var href = $(this).data("href");
            var id = $("#modal-myApplink").attr("data-id");
            if(id){
                $("input[data-id="+id+"]").val(href);
                $("#modal-myApplink").attr("data-id","");
            }else{
                // console.log(href);
                ue.execCommand('link', {href:href});
            }

            $("#modal-myApplink .close").click();
        });
        $(".mylink-app-nav2").click(function(){
            var href = $("textarea[name=mylink_href]").val();
            if(href){
                var id = $("#modal-myApplink").attr("data-id");
                if(id){
                    $("input[data-id="+id+"]").val(href);
                    $("#modal-myApplink").attr("data-id","");
                }else{
                    ue.execCommand('link', {href:href});
                }
                $("#modal-myApplink .close").click();
                $("textarea[name=mylink_href]").val("");
            }else{
                $("textarea[name=mylink_href]").focus();
                alert("链接不能为空!");
            }
        });
        // ajax 选择商品
        $("#select-app-good-btn").click(function(){
            var kw = $("#select-app-good-kw").val();
            $.ajax({
                type: 'POST',
                url: "{!! yzWebUrl('goods.goods.getMyLinkGoods') !!}",
                data: {kw:kw},
                dataType:'json',
                success: function(data){

                    $("#select-app-goods").html("");
                    if(data){
                        $.each(data,function(n,value){
                            console.log(value);
                            var html = '<div class="good">';
                            html+='<div class="img"><img src="'+value.thumb+'"/></div>'
                            html+='<div class="choosebtn">';
                            html+='<a href="javascript:;" class="mylink-app-nav" data-href="/pages/detail_v2/detail_v2?id='+value.id+'">详情链接</a><br>';
                            /*if(value.hasoption==0){
                                html+='<a href="javascript:;" class="mylink-app-nav" data-href="">下单链接</a>';
                            }*/
                            //id="other-1" ng-click="chooseLink(1, 'other-1')"
                            html+='</div>';
                            html+='<div class="info">';
                            html+='<div class="info-title">'+value.title+'</div>';
                            html+='<div class="info-price">原价:￥'+value.market_price+' 现价￥'+value.price+'</div>';
                            html+='</div>'
                            html+='</div>';
                            $("#select-app-goods").append(html);
                        });
                    }
                }
            });
        });

    })
</script>