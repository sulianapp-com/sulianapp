@extends('layouts.base')
@section('title','订单列表')

@section('content')

    <link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <div class="w1200 m0a">
        <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
        <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
        <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

        <div class="rightlist">
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="card">
                        <div class="card-header card-header-icon" data-background-color="rose">
                            <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                        </div>
                        <div class="card-content">
                            <h4 class="card-title">订单管理</h4>
                            <form  action="" method="get" class="form-horizontal" id="form1">
                                @section('form')
                                    <input type="hidden" name="c" value="site"/>
                                    <input type="hidden" name="a" value="entry"/>
                                    <input type="hidden" name="m" value="yun_shop"/>
                                    <input type="hidden" name="do" value="order" id="form_do"/>
                                    <input type="hidden" name="route" value="{{$url}}" id="form_p"/>
                                @show
                                <div>
                                    @section('search_bar')
                                        @if($route == 'order.list.waitSend')
                                            <div class="form-group  col-md-2 col-sm-6">
                                                <select name="search[sort]" class="form-control">
                                                    <option value="" @if(!$requestSearch['sort'])  selected="selected"@endif>
                                                        时间排序
                                                    </option>
                                                    <option value="1" @if($requestSearch['sort'] == 1)  selected="selected"@endif>
                                                        会员排序
                                                    </option>
                                                </select>
                                            </div>
                                        @endif
                                        <div class="form-group  col-md-2 col-sm-6">
                                            <select name="search[ambiguous][field]" id="ambiguous-field"
                                                    class="form-control">
                                                <option value="order"
                                                        @if(array_get($requestSearch,'ambiguous.field','') =='order')  selected="selected"@endif >
                                                    订单号/支付号
                                                </option>
                                                <option value="member"
                                                        @if( array_get($requestSearch,'ambiguous.field','')=='member')  selected="selected"@endif>
                                                    用户姓名/ID/昵称/手机号
                                                </option>

                                                <option value="address"
                                                        @if( array_get($requestSearch,'ambiguous.field','')=='address')  selected="selected"@endif>
                                                       收货地址/姓名/手机号
                                                </option>

                                                <option value="goods_id"{{--order_goods--}}
                                                        @if( array_get($requestSearch,'ambiguous.field','')=='goods_id')  selected="selected"@endif>
                                                    商品名称/ID
                                                </option>
                                                {{--<option value="goods_id"--}}
                                                        {{--@if( array_get($requestSearch,'ambiguous.field','')=='goods_id')  selected="selected"@endif>--}}
                                                    {{--商品ID--}}
                                                {{--</option>--}}
                                                <option value="dispatch"
                                                        @if( array_get($requestSearch,'ambiguous.field','')=='dispatch')  selected="selected"@endif>
                                                    快递单号
                                                </option>

                                                <option value="coupon"
                                                        @if( array_get($requestSearch,'ambiguous.field','')=='coupon')  selected="selected"@endif>
                                                    优惠券名称
                                                </option>
                                            </select>
                                        </div>
                                        <div class='form-group col-sm-4 col-lg-3 col-xs-12'>

                                            <input class="form-control" name="search[ambiguous][string]" type="text"
                                                   value="{{array_get($requestSearch,'ambiguous.string','')}}"
                                                   placeholder="订单号/支付单号" id="string">


                                            <div class="form-group notice" id="goods_name">
                                                <div >
                                                    <div class='input-group'>
                                                        <input type="hidden" id="plugin_id" name="plugin_id" value="@if(!empty($list['plugin_id'])) {{$list['plugin_id']}} @else 0 @endif">
                                                        <input type="text" name="search[ambiguous][name]" maxlength="30" value="{{array_get($requestSearch,'ambiguous.name','')}}" id="saler" class="form-control" readonly />
                                                        <div class='input-group-btn'>
                                                            <button class="btn btn-default" type="button" onclick="popwin = $('#modal-module-menus-notice').modal();">选择商品</button>
                                                            <button class="btn btn-danger" type="button" onclick="$('#noticeopenid').val('');$('#saler').val('');$('#saleravatar').hide()">清除选择</button>
                                                        </div>
                                                    </div>
                                                    <div id="modal-module-menus-notice"  class="modal fade" tabindex="-1">
                                                        <div class="modal-dialog" style='width: 920px;'>
                                                            <div class="modal-content">
                                                                <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择商品名称</h3></div>
                                                                <div class="modal-body" >
                                                                    <div class="row">
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control" name="keyword" value="" id="search-kwd-notice" placeholder="请输入商品名称" />
                                                                            <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_members();">搜索</button></span>
                                                                        </div>
                                                                    </div>
                                                                    <div id="module-menus-notice" style="padding-top:5px;"></div>
                                                                </div>
                                                                <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group form-group col-sm-8 col-lg-2 col-xs-12">
                                            <!-- 注意，由于属于支付宝支付的支付方式有好几种，包括app支付宝支付方式，支付宝-YZ方式
                                            等，所以进行了分组,支付选项传入的支付方式是支付方式组的id，并不是支付方式的id -->
                                            <select name="search[pay_type]" class="form-control">
                                                <option value=""
                                                        @if( array_get($requestSearch,'pay_type',''))  selected="selected"@endif>
                                                    全部支付方式
                                                </option>
                                                <option value="1"
                                                        @if( array_get($requestSearch,'pay_type','') == '1')  selected="selected"@endif>
                                                    微信支付
                                                </option>
                                                <option value="2"
                                                        @if( array_get($requestSearch,'pay_type','') == '2')  selected="selected"@endif>
                                                    支付宝支付
                                                </option>
                                                <option value="3"
                                                        @if( array_get($requestSearch,'pay_type','') == '3')  selected="selected"@endif>
                                                    余额支付
                                                </option>
                                                <option value="4"
                                                        @if( array_get($requestSearch,'pay_type','') == '4')  selected="selected"@endif>
                                                    后台付款
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group form-group col-sm-8 col-lg-2 col-xs-12">
                                            <select name="search[first_order]" class="form-control">
                                                <option value=""
                                                        @if(!$requestSearch['first_order'])  selected="selected"@endif>
                                                    不搜索首单
                                                </option>
                                                <option value="1"
                                                        @if($requestSearch['first_order'] == '1')  selected="selected"@endif>
                                                    搜索首单
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group col-sm-12 col-lg-12 col-xs-12"></div>
                                        <div class="form-group col-sm-8 col-lg-5 col-xs-12">

                                            <select name="search[time_range][field]" class="form-control form-time" >
                                                <option value=""
                                                        @if( array_get($requestSearch,'time_range.field',''))selected="selected"@endif >
                                                    操作时间
                                                </option>
                                                <option value="create_time"
                                                        @if( array_get($requestSearch,'time_range.field','')=='create_time')  selected="selected"@endif >
                                                    下单
                                                </option>
                                                <option value="pay_time"
                                                        @if( array_get($requestSearch,'time_range.field','')=='pay_time')  selected="selected"@endif>
                                                    付款
                                                </option>
                                                <option value="send_time"
                                                        @if( array_get($requestSearch,'time_range.field','')=='send_time')  selected="selected"@endif>
                                                    发货
                                                </option>
                                                <option value="finish_time"
                                                        @if( array_get($requestSearch,'time_range.field','')=='finish_time')  selected="selected"@endif>
                                                    完成
                                                </option>
                                            </select>
                                            {!!
                                                app\common\helpers\DateRange::tplFormFieldDateRange('search[time_range]', [
                                        'starttime'=>array_get($requestSearch,'time_range.start',0),
                                        'endtime'=>array_get($requestSearch,'time_range.end',0),
                                        'start'=>0,
                                        'end'=>0
                                        ], true)!!}

                                        </div>
                                    @show
                                </div>

                                <div class="form-group">

                                    <div class="col-sm-7 col-lg-9 col-xs-12">
                                        <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                                        @section('export')
                                            <button type="submit" name="export" value="1" id="export" class="btn btn-info">导出
                                                Excel
                                            </button>
                                        @if(app('plugins')->isEnabled('team-dividend'))
                                            <button type="submit" name="direct_export" value="1" id="direct-export" class="btn btn-info">导出
                                                直推 Excel（经销商）
                                            </button>
                                        @endif
                                        @show
                                        @if( $requestSearch['plugin'] != "fund")
                                            <a class="btn btn-warning"
                                               href="{!! yzWebUrl('order.export') !!}">自定义导出</a>

                                        @endif
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    {{--<form action="" method="get" class="form-horizontal" id="form1">--}}


                    {{--</form>--}}
                </div>
            </div>


            <div class="panel panel-default">
                <table class='table'
                       style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                    <tr class='trhead'>
                        <td colspan='8' style="text-align: left;">
                            订单数: <span id="total">{{$list['total']}}</span>
                            订单金额: <span id="totalmoney" style="color:red">{{$total_price}}</span>元&nbsp;
                            @section('supplier_apply')

                            @show
                        </td>
                    </tr>
                </table>

                @section('is_plugin')
                    @foreach ($list['data'] as $order_index => $order)
                        <div class="order-info">
                            <table class='table order-title'>
                                <tr>
                                    <td class="left" colspan='8'>
                                        <b>订单编号:</b> {{$order['order_sn']}}
                                        @if($order['status']>\app\common\models\Order::WAIT_PAY && isset($order['has_one_order_pay']))
                                            <b>支付单号:</b> {{$order['has_one_order_pay']['pay_sn']}}
                                        @endif
                                        <b>下单时间: </b>{{$order['create_time']}}
                                        @if( $order['has_one_refund_apply'] == \app\common\models\refund\RefundApply::WAIT_RECEIVE_RETURN_GOODS)
                                            <label class='label label-primary'>客户已经寄出快递</label>@endif

                                        <label class="label label-info">{{$order['shop_name']}}</label>
                                        @if(!empty($order['order_deliver']))
                                            <label class="label label-primary">自提点:{{$order['order_deliver']['deliver_name']}}</label>
                                        @endif
                                        @if(!empty($order['has_one_refund_apply']))
                                            <label class="label label-danger">{{$order['has_one_refund_apply']['refund_type_name']}}
                                                :{{$order['has_one_refund_apply']['status_name']}}</label>
                                    @endif
                                    @if($order['has_many_first_order'])
                                        <label class='label label-primary'>首单</label>
                                    @endif


                                    <td class="right">
                                        @if(empty($order['status']))
                                            <a class="btn btn-default btn-sm" href="javascript:;"
                                               onclick="$('#modal-close').find(':input[name=order_id]').val('{{$order['id']}}')"
                                               data-toggle="modal" data-target="#modal-close">关闭订单</a>
                                        @elseif( $order['status'] > 0 && $order['canRefund'] )
                                            <a class="btn btn-default btn-sm" href="javascript:;"
                                               onclick="$('#modal-manual-refund').find(':input[name=order_id]').val('{{$order['id']}}')"
                                               data-toggle="modal" data-target="#modal-manual-refund">退款并关闭订单</a>
                                        @endif

                                    </td>


                                </tr>
                            </table>
                            <table class='table order-main'>
                                @foreach( $order['has_many_order_goods'] as $order_goods_index => $order_goods)
                                    <tr class='trbody'>
                                        <td class="goods_info">
                                            <img src="{{tomedia($order_goods['thumb'])}}">
                                        </td>
                                        <td class="top" valign='top' style="font-size: 16px;color: #AEB9C0">
                                            <a href="{{yzWebUrl('goods.goods.edit', array('id' => $order_goods['goods_id']))}}">{{$order_goods['title']}}</a>
                                            @if( !empty($order_goods['goods_option_title']))<br/>
                                                <span style="font-size: 15px;color: #AEB9C0">{{$order_goods['goods_option_title']}}</span>

                                            @endif
                                            <br/><span style="font-size: 15px;color: #AEB9C0">{{$order_goods['goods_sn']}}</span>

                                        </td>
                                        <td class="price">
                                            原价: {{ number_format($order_goods['goods_price']/$order_goods['total'],2)}}
                                            <br/>应付: {{ number_format($order_goods['price']/$order_goods['total'],2) }}
                                            <br/>数量: {{$order_goods['total']}}
                                        </td>


                                        @if( $order_goods_index == 0)
                                            <td rowspan="{{count($order['has_many_order_goods'])}}">
                                                <a href="{!! yzWebUrl('member.member.detail',array('id'=>$order['belongs_to_member']['uid'])) !!}"> {{$order['belongs_to_member']['nickname']}}</a>
                                                <br/>
                                                {{$order['belongs_to_member']['realname']}}
                                                <br/>{{$order['belongs_to_member']['mobile']}}
                                            </td>

                                            <td rowspan="{{count($order['has_many_order_goods'])}}">
                                                <label class='label label-info'>{{$order['pay_type_name']}}</label>
                                                <br/>

                                                {{$order['has_one_dispatch_type']['name']}}
                                            </td>
                                            <td rowspan="{{count($order['has_many_order_goods'])}}" style='width:18%;'>
                                                <table class="goods-price">
                                                    <tr>
                                                        <td style=''>商品小计：</td>
                                                        <td style=''>￥{!! number_format(
                                                $order['goods_price'] ,2) !!}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td style=''>运费：</td>
                                                        <td style=''>￥{!! number_format(
                                                $order['dispatch_price'],2) !!}
                                                        </td>
                                                    </tr>
                                                    @if($order['change_price'] != 0)
                                                        <tr>
                                                            <td style=''>卖家改价：</td>
                                                            <td style='color:green'>￥{!! number_format(
                                                $order['change_price'] ,2) !!}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($order['change_dispatch_price'] != 0)
                                                        <tr>
                                                            <td style=''>卖家改运费：</td>
                                                            <td style='color:green'>￥{{ number_format(
                                                $order['change_dispatch_price'] ,2) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td style=''>应收款：</td>
                                                        <td style='color:green'>￥{!! number_format(
                                                $order['price'] ,2) !!}
                                                        </td>
                                                    </tr>
                                                    @if($order['status'] == 0)
                                                        <tr>
                                                            <td></td>
                                                            <td style='color:green;'>
                                                                <a href="javascript:;" class="btn btn-link "
                                                                   onclick="changePrice('{{$order['id']}}')">修改价格</a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </td>
                                            <td rowspan="{{count($order['has_many_order_goods'])}}"><label
                                                        class='label label-info'>{{$order['status_name']}}</label>
                                                <br/>
                                                <a href="{!! yzWebUrl($detail_url,['id'=>$order['id']])!!}">查看详情</a>
                                            </td>
                                            <td rowspan="{{count($order['has_many_order_goods'])}}" width="10%">

                                                @include($include_ops)

                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endforeach
                @show
                @include('order.modals')
                <div id="pager">{!! $pager !!}</div>


            </div>
        </div>
    </div>
    <script>
        // $(function () {
        //     $("#ambiguous-field").on('change', function () {
        //
        //         $(this).next('input').attr('placeholder', $(this).find(':selected').text().trim())
        //     });
        // })

        $(function () {

            // $("#goods_name").hide();//页面加载，隐藏选择商品名称控件

            $("#ambiguous-field").on('change', function () {
                $(this).next('input').attr('placeholder', $(this).find(':selected').text().trim())


                if ($(this).val()=='goods_id'){//选择商品名称搜索
                    $("#string").hide();
                    $("#goods_name").show();
                }else {
                    $('input[name="search[ambiguous][string]"]').val("");
                    $("#goods_name").hide();
                    $("#string").show();
                }

            });
        })

        if ($("#ambiguous-field").val()=='goods_id'){//选择商品名称搜索
            $("#string").hide();
            $("#goods_name").show();
        }else {
            $("#goods_name").hide();
            $("#string").show();
        }

        function search_members() {
            if ($('#search-kwd-notice').val() == '') {
                Tip.focus('#search-kwd-notice', '请输入关键词');
                return;
            }
            $("#module-menus-notice").html("正在搜索....");
            $.get("{!! yzWebUrl('goods.goods.search-order') !!}", {
                keyword: $.trim($('#search-kwd-notice').val()),
                plugin_id : $.trim($('#plugin_id').val()),
            }, function (dat) {
                $('#module-menus-notice').html(dat);
            });
        }

        function select_good (o) {
            console.log(o.id)
            $('input[name="search[ambiguous][string]"]').val(o.id);
            $('input[name="search[ambiguous][name]"]').val(o.title);
            $("#saleravatar").show();
            $("#saleravatar").find('img').attr('src', o.thumb);
            $("#saler").val(o.title);
            $("#modal-module-menus-notice .close").click();
        }


    </script>
@section('plugin_js')
@show
@endsection('content')