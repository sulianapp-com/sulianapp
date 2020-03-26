@extends('layouts.base')

@section('content')

<div id="qrcode" ref="qrcode" style="display:none;"></div>
<div class="w1200 m0a">
    <div class="rightlist">
        <!-- 商城入口二维码 -->
        <div class="panel panel-info">
            <div class="panel-heading">商城页面链接</div>
            <div class="panel-body">
                <ul class="row dimension">
                    <li>
                        <p class="p1">商城首页</p>
                        <img id='home'>
                        <input type="hidden" >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('home') !!}" data-url="{!! yzAppFullUrl('home') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">分类导航</p>
                        
                        <img id='category'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('category') !!}" data-url="{!! yzAppFullUrl('category') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">全部商品</p>

                        <img id='searchAll'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('searchAll') !!}" data-url="{!! yzAppFullUrl('searchAll') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                </ul>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">会员中心链接</div>
            <div class="panel-body">
                <ul class="dimension">
                    <li>
                        <p class="p1">会员中心</p>


                        <img id='member'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member') !!}" data-url="{!! yzAppFullUrl('member') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">我的订单</p>

                        <img id='orderList'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/orderList/0') !!}" data-url="{!! yzAppFullUrl('member/orderList/0') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">购物车</p>

                        <img id='cart'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('cart') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">我的收藏</p>

                        <img id='collection'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/collection') !!}" data-url="{!! yzAppFullUrl('member/collection') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">我的足迹</p>

                        <img id='footprint'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/footprint') !!}" data-url="{!! yzAppFullUrl('member/footprint') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">评价</p>

                        <img id='myEvaluation'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/myEvaluation') !!}" data-url="{!! yzAppFullUrl('member/myEvaluation') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">关系</p>

                        <img id='myrelationship'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/myrelationship') !!}" data-url="{!! yzAppFullUrl('member/myrelationship') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">收货地址</p>

                        <img id='address'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/address') !!}" data-url="{!! yzAppFullUrl('member/address') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">我的优惠券</p>

                        <img id='coupon_index'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('coupon/coupon_index') !!}" data-url="{!! yzAppFullUrl('coupon/coupon_index') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">领券中心</p>

                        <img id='coupon_store'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('coupon/coupon_store') !!}" data-url="{!! yzAppFullUrl('coupon/coupon_store') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">积分页面</p>

                        <img id='integral_v2'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/integral_v2') !!}" data-url="{!! yzAppFullUrl('member/integral_v2') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">积分明细</p>

                        <img id='integrallist'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/integrallist') !!}" data-url="{!! yzAppFullUrl('member/integrallist') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">余额页面</p>

                        <img id='balance'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/balance') !!}" data-url="{!! yzAppFullUrl('member/balance') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">余额明细</p>

                        <img id='detailed'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/detailed') !!}" data-url="{!! yzAppFullUrl('member/detailed') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>

                </ul>
            </div>
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">我的推广链接</div>
            <div class="panel-body">
                <ul class="dimension">
                    <li>
                        <p class="p1">推广中心</p>

                         <img id='extension'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/extension') !!}" data-url="{!! yzAppFullUrl('member/extension') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">收入明细</p>

                        <img id='incomedetails'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/incomedetails') !!}" data-url="{!! yzAppFullUrl('member/incomedetails') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">收入提现</p>

                        <img id='income'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/income') !!}" data-url="{!! yzAppFullUrl('member/income') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                    <li>
                        <p class="p1">提现明细</p>

                         <img id='presentationRecord'  >
                        <h5><a href="javascript:;" data-clipboard-text="{!! yzAppFullUrl('member/presentationRecord') !!}" data-url="{!! yzAppFullUrl('member/presentationRecord') !!}" class="js-clip" title="复制链接">复制链接</a></h5>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
 <style type="text/css">
    .dimension>li{
        width:300px;
        float:left;
        text-align:center;
        margin-bottom:20px;
        border-bottom:1px dotted rgba(0,0,0,.1);
        padding-bottom:20px;
    }
    .dimension>li>img{
        width:120px;
        height:120px;
        border:1px solid #aaa;
        border:0;
    }
    .dimension>li>p{
        text-align:center;
        font-weight:bold;
     }
    .p1 {
        margin: 0 0 30px;
    }
    h5{
        margin-top: 30px;
    }
 </style>
<script src="{{resource_get('static/js/qrcode.min.js')}}"></script>
<script>

    qrcodeScan("{!! yzAppFullUrl('home') !!}",'home');
    qrcodeScan("{!! yzAppFullUrl('category') !!}",'category');
    qrcodeScan("{!! yzAppFullUrl('searchAll') !!}",'searchAll');
    qrcodeScan("{!! yzAppFullUrl('member') !!}",'member');
    qrcodeScan("{!! yzAppFullUrl('member/orderList/0') !!}",'orderList'); 
    qrcodeScan("{!! yzAppFullUrl('cart') !!}",'cart');
    qrcodeScan("{!! yzAppFullUrl('member/collection') !!}",'collection');
    qrcodeScan("{!! yzAppFullUrl('member/footprint') !!}",'footprint');
    qrcodeScan("{!! yzAppFullUrl('member/myEvaluation') !!}",'myEvaluation');
    qrcodeScan("{!! yzAppFullUrl('member/myrelationship') !!}",'myrelationship');
    qrcodeScan("{!! yzAppFullUrl('member/address') !!}",'address');
    qrcodeScan("{!! yzAppFullUrl('coupon/coupon_index') !!}",'coupon_index');
    qrcodeScan("{!! yzAppFullUrl('coupon/coupon_store') !!}",'coupon_store');
    qrcodeScan("{!! yzAppFullUrl('member/integral_v2') !!}",'integral_v2');
    qrcodeScan("{!! yzAppFullUrl('member/integrallist') !!}",'integrallist');
    qrcodeScan("{!! yzAppFullUrl('member/balance') !!}",'balance');
    qrcodeScan("{!! yzAppFullUrl('member/detailed') !!}",'detailed');
    qrcodeScan("{!! yzAppFullUrl('member/extension') !!}",'extension');
    qrcodeScan("{!! yzAppFullUrl('member/incomedetails') !!}",'incomedetails');
    qrcodeScan("{!! yzAppFullUrl('member/income') !!}",'income');
    qrcodeScan("{!! yzAppFullUrl('member/presentationRecord') !!}",'presentationRecord');
 

    function qrcodeScan(url,name) {//生成二维码
        let qrcode = new QRCode('qrcode', {
            width: 200,  // 二维码宽度
            height: 200, // 二维码高度
            render: 'image',
            text: url 
        });
        var data = $("canvas")[$("canvas").length - 1].toDataURL().replace("image/png", "image/octet-stream;");
        $('#'+name+'').attr('src',data);
        this.img = data; 
    }




</script>

@endsection





