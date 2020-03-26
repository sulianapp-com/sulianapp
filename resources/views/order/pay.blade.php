@extends('layouts.base')
@section('title','订单支付')

@section('content')
<div>
    <a href="#" class="btn-do-it">支付</a>
</div>
<script type="text/javascript">
    define = null;
    require = null;
</script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config({"debug":false,"beta":false,"appId":"wx6be17f352e859277","nonceStr":"Jyao2CmYEM","timestamp":"1493283881","url":"http:\/\/test.yunzshop.com\/addons\/yun_shop\/api.php?i=2&mid=null&type=1&route=order.merge-pay.wechatPay&order_pay_id=143&i=2&type=1","signature":"c14af357bb46c4d74ffb59d64fdf2899ef975bab","jsApiList":["chooseWXPay"]});
    </script>
    <script>
        $(function(){

            $(".btn-do-it").click(function(){
                wx.chooseWXPay({
                    appId: "wx6be17f352e859277",
                    timestamp: 1493283881, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                    nonceStr: '5901b4296438e', // 支付签名随机串，不长于 32 位
                    package: 'prepay_id=wx2017042717044147947bae970939261686', // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                    signType: 'MD5', // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                    paySign: 'B7EB067657558BF99EF1767DEEFF9DFC', // 支付签名
                    success: function (res) {
                        // 支付成功后的回调函数
                        if(res.errMsg == "chooseWXPay:ok" ) {
                            alert('支付成功。');
                        }else{
                            console.log(res);
                            alert("支付失败，请返回重试。");
                        }
                    },
                    fail: function (res) {
                        console.log(res);
                        alert("支付失败，请返回重试。");
                    }
                });
            });
        });
    </script>
@endsection

