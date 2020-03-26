@extends('layouts.base')
@section('title', '微信开放平台数据更新')
@section('content')
    <style>
        .loadEffect{
            display:none;
            width: 100px;
            height: 100px;
            position: absolute;
            left: 50%;
            margin: 0 auto;
            margin-top:100px;
        }
        .loadEffect span{
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: lightgreen;
            position: absolute;
            -webkit-animation: load 1.04s ease infinite;
        }
        @-webkit-keyframes load{
            0%{
                opacity: 1;
            }
            100%{
                opacity: 0.2;
            }
        }
        .loadEffect span:nth-child(1){
            left: 0;
            top: 50%;
            margin-top:-8px;
            -webkit-animation-delay:0.13s;
        }
        .loadEffect span:nth-child(2){
            left: 14px;
            top: 14px;
            -webkit-animation-delay:0.26s;
        }
        .loadEffect span:nth-child(3){
            left: 50%;
            top: 0;
            margin-left: -8px;
            -webkit-animation-delay:0.39s;
        }
        .loadEffect span:nth-child(4){
            top: 14px;
            right:14px;
            -webkit-animation-delay:0.52s;
        }
        .loadEffect span:nth-child(5){
            right: 0;
            top: 50%;
            margin-top:-8px;
            -webkit-animation-delay:0.65s;
        }
        .loadEffect span:nth-child(6){
            right: 14px;
            bottom:14px;
            -webkit-animation-delay:0.78s;
        }
        .loadEffect span:nth-child(7){
            bottom: 0;
            left: 50%;
            margin-left: -8px;
            -webkit-animation-delay:0.91s;
        }
        .loadEffect span:nth-child(8){
            bottom: 14px;
            left: 14px;
            -webkit-animation-delay:1.04s;
        }

    </style>
    <div class='panel panel-default'>
        <div class="loadEffect"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
    </div>

    <script>
        var loop = true;
        var _that = this;

        var query = setInterval(function () {
            console.log('---------loop--------' + _that.loop);

            if (_that.loop) {
                $.ajax({
                    url: '{!! yzWebUrl('member.member.updateWechatData') !!}',
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        $('.loadEffect').show();
                    }
                }).done(function (json) {
                    console.log(json.status);

                    if (json.status == 1) {
                        _that.loop = false;
                    }

                }).fail(function (message) {
                    console.log('fail:', message)
                    location.href = '{!! yzWebUrl('member.member.updateWechatOpenData', ['status' => 0]) !!}';
                }).always(function () {
                   // $('.loadEffect').hide();
                });
            } else {
                clearInterval(query);

                location.href = '{!! yzWebUrl('member.member.updateWechatOpenData', ['status' => 1]) !!}';
            }

        }, 1000);
    </script>
@endsection