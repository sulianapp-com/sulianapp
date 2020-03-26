@extends('layouts.base')

@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->

            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

                <div class='alert alert-info alert-important'>
                    请将公众平台模板消息所在行业选择为： IT科技/互联网|电子商务<br>
                    提示：点击模版消息后方开关按钮<input class="mui-switch" type="checkbox" disabled/>即可开启默认模版消息，无需进行额外设置。<br>
                    如需进行消息推送个性化消息，点击进入自定义模版管理。
                </div>

                <div class="panel panel-default">
                    <style type='text/css'>
                        .multi-item {
                            height: 110px;
                        }

                        .img-thumbnail {
                            width: 100px;
                            height: 100px
                        }

                        .img-nickname {
                            position: absolute;
                            bottom: 0px;
                            line-height: 25px;
                            height: 25px;
                            color: #fff;
                            text-align: center;
                            width: 90px;
                            bottom: 55px;
                            background: rgba(0, 0, 0, 0.8);
                            left: 5px;
                        }

                        .multi-img-details {
                            padding: 5px;
                        }
                    </style>

                    <div class='panel-heading'>
                        余额变动通知
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额变动通知</label>
                            <div class="col-sm-6 col-xs-12">
                                @foreach ($temp_list as $item)
                                    @if('账户余额提醒'== $item['title'])
                                        <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                        <input type="text" value="{{$item['id']}}" style= "display:none">
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-sm-4 col-xs-2">
                                @foreach ($temp_list as $item)
                                    @if('账户余额提醒'== $item['title'])
                                        <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                               @if($item['is_open'] == '1')
                                               checked
                                               @endif
                                               onclick="message_default({{$item['id']}})"/>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class='panel-heading'>
                        卖家通知
                    </div>
                    <div class='panel-body'>
                        <br>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买商品通知</label>
                            <div class="col-sm-6 col-xs-12">
                                @foreach ($temp_list as $item)
                                    @if('购买成功通知'== $item['title'])
                                        <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                        <input type="text" value="{{$item['id']}}" style= "display:none">
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-sm-4 col-xs-2">
                                @foreach ($temp_list as $item)
                                    @if('购买成功通知'== $item['title'])
                                        <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                               @if($item['is_open'] == '1')
                                               checked
                                               @endif
                                               onclick="message_default({{$item['id']}})"/>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            {{--<div class="col-sm-2 col-xs-6">--}}
                            {{--<input class="mui-switch mui-switch-animbg" id="seller_order_create" type="checkbox"--}}
                            {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['seller_order_create']))--}}
                            {{--checked--}}
                            {{--@endif--}}
                            {{--onclick="message_default(this.id)"/>--}}
                            {{--</div>--}}
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单生成通知[卖家]</label>
                            <div class="col-sm-6 col-xs-12">
                                @foreach ($temp_list as $item)
                                    @if('订单生成通知'== $item['title'])
                                        <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                        <input type="text" value="{{$item['id']}}" style= "display:none">
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-sm-4 col-xs-2">
                                @foreach ($temp_list as $item)
                                    @if('订单生成通知'== $item['title'])
                                       <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                           @if($item['is_open'] == '1')
                                               checked
                                           @endif
                                               onclick="message_default({{$item['id']}})"/>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            {{--<div class="col-sm-2 col-xs-6">--}}
                                {{--<input class="mui-switch mui-switch-animbg" id="seller_order_create" type="checkbox"--}}
                                       {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['seller_order_create']))--}}
                                       {{--checked--}}
                                       {{--@endif--}}
                                       {{--onclick="message_default(this.id)"/>--}}
                            {{--</div>--}}
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单支付提醒[卖家]</label>
                            <div class="col-sm-6 col-xs-12">
                                @foreach ($temp_list as $item)
                                    @if('订单支付提醒' == $item['title'])
                                        <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                        <input type="text" value="{{$item['id']}}"  style= "display:none">
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-sm-4 col-xs-2">
                                @foreach ($temp_list as $item)
                                    @if('订单支付提醒'== $item['title'])
                                        <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                               @if($item['is_open'] == '1')
                                               checked
                                               @endif
                                               onclick="message_default({{$item['id']}})"/>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            {{--<div class="col-sm-2 col-xs-6">--}}
                                {{--<input class="mui-switch mui-switch-animbg" id="seller_order_pay" type="checkbox"--}}
                                       {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['seller_order_pay']))--}}
                                       {{--checked--}}
                                       {{--@endif--}}
                                       {{--onclick="message_default(this.id)"/>--}}
                            {{--</div>--}}
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单完成通知[卖家]</label>
                            <div class="col-sm-6 col-xs-12">
                                @foreach ($temp_list as $item)
                                    @if('订单完成通知' == $item['title'])
                                        <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                        <input type="text" value="{{$item['id']}}" style= "display:none">
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-sm-4 col-xs-2">
                                @foreach ($temp_list as $item)
                                    @if('订单完成通知'== $item['title'])
                                        <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                               @if($item['is_open'] == '1')
                                               checked
                                               @endif
                                               onclick="message_default({{$item['id']}})"/>
                                        @break
                                    @endif
                                @endforeach
                            </div>
                            {{--<div class="col-sm-2 col-xs-6">--}}
                                {{--<input class="mui-switch mui-switch-animbg" id="seller_order_finish" type="checkbox"--}}
                                       {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['seller_order_finish']))--}}
                                       {{--checked--}}
                                       {{--@endif--}}
                                       {{--onclick="message_default(this.id)"/>--}}
                            {{--</div>--}}
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6 col-xs-12">
                                <div class='input-group'>
                                    <input type="text" id='salers' name="salers" maxlength="30"
                                           value="@foreach ($set['salers'] as $saler) {{ $saler['nickname'] }} @endforeach"
                                           class="form-control" readonly/>
                                    <div class='input-group-btn'>
                                        <button class="btn btn-default" type="button"
                                                onclick="popwin = $('#modal-module-menus').modal();">选择通知人
                                        </button>
                                    </div>
                                </div>
                                <div class="input-group multi-img-details" id='saler_container'>
                                    @foreach ($set['salers'] as $saler)
                                        <div class="multi-item saler-item" openid='{{ $saler['openid'] }}'>
                                            <img class="img-responsive img-thumbnail" src='{{ $saler['avatar'] }}'
                                                 onerror="this.src='{{static_url('resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                                            <div class='img-nickname'>{{ $saler['nickname'] }}</div>
                                            <input type="hidden" value="{{ $saler['openid'] }}"
                                                   name="yz_notice[salers][{{ $saler['uid'] }}][openid]">
                                            <input type="hidden" value="{{ $saler['uid'] }}"
                                                   name="yz_notice[salers][{{ $saler['uid'] }}][uid]">
                                            <input type="hidden" value="{{ $saler['nickname'] }}"
                                                   name="yz_notice[salers][{{ $saler['uid'] }}][nickname]">
                                            <input type="hidden" value="{{ $saler['avatar'] }}"
                                                   name="yz_notice[salers][{{ $saler['uid'] }}][avatar]">
                                            <em onclick="remove_member(this)" class="close">×</em>
                                        </div>
                                    @endforeach
                                </div>
                                <span class='help-block'>订单生成后商家通知，可以指定多个人，如果不填写则不通知</span>
                                <div id="modal-module-menus" class="modal fade" tabindex="-1">
                                    <div class="modal-dialog" style='width: 920px;'>
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button aria-hidden="true" data-dismiss="modal" class="close"
                                                        type="button">×
                                                </button>
                                                <h3>选择通知人</h3></div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="keyword" value=""
                                                               id="search-kwd" placeholder="请输入粉丝昵称/姓名/手机号"/>
                                                        <span class='input-group-btn'><button type="button"
                                                                                              class="btn btn-default"
                                                                                              onclick="search_members();">
                                                                搜索
                                                            </button></span>
                                                    </div>
                                                </div>
                                                <div id="module-menus" style="padding-top:5px;"></div>
                                            </div>
                                            <div class="modal-footer"><a href="#" class="btn btn-default"
                                                                         data-dismiss="modal" aria-hidden="true">关闭</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知方式</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="1" name='yz_notice[notice_enable][created]'
                                           @if ($set['notice_enable']['created']) checked @endif /> 下单通知
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="1" name='yz_notice[notice_enable][paid]'
                                           @if ($set['notice_enable']['paid']) checked @endif /> 付款通知
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" value="1" name='yz_notice[notice_enable][received]'
                                           @if ($set['notice_enable']['received']) checked @endif /> 买家确认收货通知
                                </label>
                                <div class="help-block">通知商家方式</div>
                            </div>
                        </div>

                    </div>
                    <div class='panel-heading'>
                        买家通知
                    </div>
                    <div class='panel-body'>
                        @if(YunShop::notice()->getNotSend('order_submit_success'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单提交成功通知</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('订单提交成功通知' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input  type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('订单提交成功通知'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_submit_success" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_submit_success']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif

                        @if(YunShop::notice()->getNotSend('order_cancel'))
                                <br>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单取消通知</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('订单取消通知' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input  type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('订单取消通知'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_cancel" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_cancel']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_pay_success'))
                                <br>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单支付成功通知</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('订单支付成功通知' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input  type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('订单支付成功通知'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_pay_success" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_pay_success']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_send'))
                                <br>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单发货提醒</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('订单发货提醒' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input  type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('订单发货提醒'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_send" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_send']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_finish'))
                                <br>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">确认收货通知</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('确认收货通知' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input  type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('确认收货通知'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_finish" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_finish']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_refund_apply'))
                                <br>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款申请通知</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('退款申请通知' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input  type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('退款申请通知'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_refund_apply" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_refund_apply']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_refund_success'))
                                <br>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款成功通知</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('退款成功通知' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input  type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('退款成功通知'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_refund_success" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_refund_success']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_refund_reject'))
                                <br>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款拒绝通知</label>
                                <div class="col-sm-6 col-xs-12">
                                    @foreach ($temp_list as $item)
                                        @if('退款拒绝通知' == $item['title'])
                                            <input type="text" value="{{$item['title']}}" class='form-control diy-notice' disabled="disabled">
                                            <input type="text" value="{{$item['id']}}" style= "display:none">
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-sm-4 col-xs-2">
                                    @foreach ($temp_list as $item)
                                        @if('退款拒绝通知'== $item['title'])
                                            <input class="mui-switch mui-switch-animbg" id="{{$item['id']}}" type="checkbox"
                                                   @if($item['is_open'] == '1')
                                                   checked
                                                   @endif
                                                   onclick="message_default({{$item['id']}})"/>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                                {{--<div class="col-sm-2 col-xs-6">--}}
                                    {{--<input class="mui-switch mui-switch-animbg" id="order_refund_reject" type="checkbox"--}}
                                           {{--@if(\app\common\models\notice\MinAppTemplateMessage::getOpenTemp($set['order_refund_reject']))--}}
                                           {{--checked--}}
                                           {{--@endif--}}
                                           {{--onclick="message_default(this.id)"/>--}}
                                {{--</div>--}}
                            </div>
                        @endif

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                            </div>
                        </div>
                    </div>
                    <script>
                        function message_default(name) {
                            var id = "#" + name;
                            var url_open = "{!! yzWebUrl('setting.small-program.set-notice') !!}"
                            if ($(id).is(':checked')) {
                                var postdata = {
                                    id: name,
                                    open: 1
                                };
                                //开
                                $.post(url_open,postdata,function(data){
                                    if (data.result == 1) {
                                        showPopover($(id),"开启成功")
                                    } else {
                                        showPopover($(id),"开启失败，请检查微信模版")
                                        $(id).attr("checked",false);
                                    }
                                }, "json");
                            } else {
                                var postdata = {
                                    id: name,
                                    open: 0
                                };
                                //关
                                $.post(url_open,postdata,function(data){
                                    showPopover($(id),"关闭成功")
                                }, "json");
                            }
                        }
                        function showPopover(target, msg) {
                            target.attr("data-original-title", msg);
                            $('[data-toggle="tooltip"]').tooltip();
                            target.tooltip('show');
                            target.focus();
                            //2秒后消失提示框
                            setTimeout(function () {
                                    target.attr("data-original-title", "");
                                    target.tooltip('hide');
                                }, 2000
                            );
                        }
                    </script>
                    <script language='javascript'>
                        function search_members() {
                            if ($.trim($('#search-kwd').val()) == '') {
                                Tip.focus('#search-kwd', '请输入关键词');
                                return;
                            }
                            $("#module-menus").html("正在搜索....");
                            $.get("{!! yzWebUrl('member.member.get-search-member') !!}", {
                                keyword: $.trim($('#search-kwd').val())
                            }, function (dat) {
                                $('#module-menus').html(dat);
                            });
                        }
                        function select_member(o) {
                            if ( !o.has_one_mini_app) {
                                alert(" 该会员没有进行小程序授权");
                                return;
                            }
                            if ($('.multi-item[openid="' + o.has_one_mini_app.openid + '"]').length > 0) {
                                return;
                            }
                            var html = '<div class="multi-item" openid="' + o.has_one_mini_app.openid + '">';
                            html += '<img class="img-responsive img-thumbnail" src="' + o.avatar + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
                            html += '<div class="img-nickname">' + o.nickname + '</div>';
                            html += '<input type="hidden" value="' + o.has_one_mini_app.openid + '" name="yz_notice[salers][' + o.uid + '][openid]">';
                            html += '<input type="hidden" value="' + o.nickname + '" name="yz_notice[salers][' + o.uid + '][nickname]">';
                            html += '<input type="hidden" value="' + o.avatar + '" name="yz_notice[salers][' + o.uid + '][avatar]">';
                            html += '<input type="hidden" value="' + o.uid + '" name="yz_notice[salers][' + o.uid + '][uid]">';
                            html += '<em onclick="remove_member(this)"  class="close">×</em>';
                            html += '</div>';
                            $("#saler_container").append(html);
                            refresh_members();
                        }
                        function remove_member(obj) {
                            $(obj).parent().remove();
                            refresh_members();
                        }
                        function refresh_members() {
                            var nickname = "";
                            $('.multi-item').each(function () {
                                nickname += " " + $(this).find('.img-nickname').html() + "; ";
                            });
                            $('#salers').val(nickname);
                        }
                    </script>
                    <script type="text/javascript">
                        $('.diy-notice').select2();
                    </script>
                </div>
            </form>
        </div>
    </div>
@endsection
