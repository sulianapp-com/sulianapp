@extends('layouts.base')
@section('title','订单详情')
@section('js')
    <link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <script language="javascript">
        $(function(){
            $("#myTab li.active>a").css("background","#f15353");
        })
        window.optionchanged = false;
        require(['bootstrap'], function () {
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
                $(this).css("background","#f15353").parent().siblings().children().css("background","none")
            })
        });
        function sub(url_status) {
            var order_id = $('.order_id').val();
            var remark = $('#remark').val();
            var invoice = $("[name='basic-detail[invoice]']").val();//获取发票
            
            if (url_status == 'invoice') {
                var url = "{!! yzWebUrl('order.operation.invoice') !!}";
            } 
            if (url_status == 'remark') {
                var url = "{!! yzWebUrl('order.operation.remarks') !!}";
            }
            $.post(url, {
                     {{--$.post("{!! yzWebUrl('setting.small-program.add') !!}", {--}}
                    order_id: order_id,
                    remark: remark,
                    invoice: invoice,
                }, function (json) {
                var json = $.parseJSON(json);
                if (json.result == 1) {
                    window.location.reload();
                }
            });
        }
        function showDiyInfo(obj) {
            var hide = $(obj).attr('hide');
            if (hide == '1') {
                $(obj).next().slideDown();
            }
            else {
                $(obj).next().slideUp();
            }
            $(obj).attr('hide', hide == '1' ? '0' : '1');
        }

        //cascdeInit("{!! isset($user['province'])?$user['province']:'' !!}", "{!! isset($user['city'])?$user['city']:'' !!}", "{!! isset($user['area'])?$user['area']:'' !!}");

        $('#editaddress').click(function () {
            show_address(1);
        });

        $('#backaddress').click(function () {
            show_address(0);
        });

        $('#editexpress').click(function () {
            show_express(1);
        });

        $('#backexpress').click(function () {
            show_express(0);
        });


        function show_address(flag) {
            if (flag == 1) {
                $('.ad1').hide();
                $('.ad2').show();
            } else {
                $('.ad1').show();
                $('.ad2').hide();
            }
        }
        function show_express(flag) {
            if (flag == 1) {
                $('.ex1').hide();
                $('.ex2').show();
            } else {
                $('.ex1').show();
                $('.ex2').hide();
            }
        }

    </script>
@stop

@section('content')
    <div class="w1200 m0a">

        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">订单管理 &nbsp; <i class="fa fa-angle-double-right"></i> &nbsp; 订单详情</a>
                    </li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="main">

                <input type="hidden" class="order_id" value="{{$order['id']}}"/>
                <input type="hidden" name="token" value="{{$var['token']}}"/>
                <input type="hidden" name="dispatchid" value="{{$dispatch['id']}}"/>
                <div class="panel panel-default">
                    {{--<div class="top">
                        <ul class="add-shopnav" id="myTab">
                            <li class="active"><a href="#tab_basic">基本信息</a></li>

                            @foreach(\app\backend\modules\income\Income::current()->getItem('order') as $key=>$value)
                                <li><a href="#{{$key}}">{{$value['title']}}</a></li>
                            @endforeach

                        </ul>
                    </div>--}}
                    <div class="info">
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane  active" id="tab_basic">@include('order.basicDetail')</div>
                                @foreach(\app\backend\modules\income\Income::current()->getItem('order') as $key=>$value)
                                    <div class="tab-pane"
                                         id="{{$key}}">{!! widget($value['class'], ['goods_id'=> $goods->id])!!}</div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>

@endsection('content')

