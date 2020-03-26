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
                        商城消息提醒
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城消息提醒</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='yz_notice[toggle]' value='1' @if ($set['toggle'] == 1) checked @endif/>
                                    开启
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' name='yz_notice[toggle]' value='0' @if (empty($set['toggle'])) checked @endif />
                                    关闭
                                </label>
                                <div class="help-block">
                                    消息通知开关：控制商城全部消息（包含插件消息）
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='panel-heading'>
                        积分变动通知
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分变动通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[point_change]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['point_change'])) value="{{$set['point_change']}}"
                                    selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                            @if($set['point_change'] == $item['id'])
                                            selected
                                            @endif>
                                            {{$item['title']}}
                                        </option>
                                    @endforeach

                                </select>
                                <div class="help-block">通知公众平台模板消息编号: OPENTM207509450</div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="point_change" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['point_change']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分不足通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[point_deficiency]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['point_deficiency'])) value="{{$set['point_deficiency']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['point_deficiency'] == $item['id'])
                                                selected
                                                @endif>
                                            {{$item['title']}}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class='panel-heading'>
                        余额变动通知
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额变动通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[balance_change]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['balance_change'])) value="{{$set['balance_change']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模版
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['balance_change'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                                <div class="help-block">通知公众平台模板消息编号: OPENTM401833445</div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="balance_change" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['balance_change']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额不足通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[balance_deficiency]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['balance_deficiency'])) value="{{$set['balance_deficiency']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模版
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['balance_deficiency'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class='panel-heading'>
                        卖家通知
                    </div>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">购买商品通知</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[buy_goods_msg]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['buy_goods_msg'])) value="{{$set['buy_goods_msg']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['buy_goods_msg'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="buy_goods_msg" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['buy_goods_msg']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单生成通知[卖家]</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[seller_order_create]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['seller_order_create'])) value="{{$set['seller_order_create']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['seller_order_create'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="seller_order_create" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['seller_order_create']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单支付通知[卖家]</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[seller_order_pay]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['seller_order_pay'])) value="{{$set['seller_order_pay']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['seller_order_pay'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                                <div class="help-block">通知公众平台模板消息编号: OPENTM207525131</div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="seller_order_pay" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['seller_order_pay']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单完成通知[卖家]</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[seller_order_finish]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['seller_order_finish'])) value="{{$set['seller_order_finish']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['seller_order_finish'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                                <div class="help-block">通知公众平台模板消息编号: OPENTM413711838</div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="seller_order_finish" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['seller_order_finish']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">申请退款/退货/换货申请通知[卖家]</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[order_refund_apply_to_saler]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_apply_to_saler'])) value="{{$set['order_refund_apply_to_saler']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['order_refund_apply_to_saler'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                                <div class="help-block">通知公众平台模板消息编号: OPENTM414174084</div>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="order_refund_apply_to_saler" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_apply_to_saler']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>

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
                        关系通知
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">两级消息通知</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' name='yz_notice[other_toggle]' value='1'
                                           @if ($set['other_toggle'] == 1) checked @endif/>
                                    开启</label>
                                <label class='radio-inline'>
                                    <input type='radio' name='yz_notice[other_toggle]' value='0'
                                           @if (empty($set['other_toggle'])) checked @endif />
                                    关闭</label>
                                <div class="help-block">开启：会员可以收到一级、二级下线下单、付款、发货、收货通知（使用任务处理通知）建议使用业务处理通知模板消息编号: OPENTM207574677</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='yz_notice[other_toggle_temp]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['other_toggle_temp'])) value="{{$set['other_toggle_temp']}}"
                                            selected @else value=""
                                            @endif
                                    >
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['other_toggle_temp'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="other_toggle_temp" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['other_toggle_temp']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
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
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_submit_success]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_submit_success'])) value="{{$set['order_submit_success']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_submit_success'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: OPENTM200746866</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_submit_success" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_submit_success']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_cancel'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单取消通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_cancel]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_cancel'])) value="{{$set['order_cancel']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_cancel'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: OPENTM412815063</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_cancel" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_cancel']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_pay_success'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单支付成功通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_pay_success]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_pay_success'])) value="{{$set['order_pay_success']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_pay_success'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: OPENTM204987032</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_pay_success" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_pay_success']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_send'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单发货通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_send]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_send'])) value="{{$set['order_send']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_send'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: OPENTM413713493</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_send" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_send']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_finish'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">订单确认收货通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_finish]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_finish'])) value="{{$set['order_finish']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_finish'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: OPENTM411450578</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_finish" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_finish']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_refund_apply'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款申请通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_refund_apply]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_apply'])) value="{{$set['order_refund_apply']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_refund_apply'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: TM00431</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_refund_apply" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_apply']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_refund_success'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款成功通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_refund_success]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_success'])) value="{{$set['order_refund_success']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_refund_success'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: TM00430</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_refund_success" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_success']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('order_refund_reject'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款申请驳回通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[order_refund_reject]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_reject'])) value="{{$set['order_refund_reject']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['order_refund_reject'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: TM00432</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="order_refund_reject" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['order_refund_reject']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('customer_upgrade'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员升级通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[customer_upgrade]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['customer_upgrade'])) value="{{$set['customer_upgrade']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['customer_upgrade'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: OPENTM400341556</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="customer_upgrade" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['customer_upgrade']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('withdraw_submit'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现提交通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[withdraw_submit]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['withdraw_submit'])) value="{{$set['withdraw_submit']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['withdraw_submit'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: TM00979</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="withdraw_submit" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['withdraw_submit']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('withdraw_success'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现成功通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[withdraw_success]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['withdraw_success'])) value="{{$set['withdraw_success']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['withdraw_success'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: TM00980</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="withdraw_success" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['withdraw_success']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif
                        @if(YunShop::notice()->getNotSend('withdraw_fail'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现失败通知</label>
                                <div class="col-sm-8 col-xs-12">
                                    <select name='yz_notice[withdraw_fail]' class='form-control diy-notice'>
                                        <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['withdraw_fail'])) value="{{$set['withdraw_fail']}}"
                                                selected @else value=""
                                                @endif
                                        >
                                            默认消息模板
                                        </option>
                                        @foreach ($temp_list as $item)
                                            <option value="{{$item['id']}}"
                                                    @if($set['withdraw_fail'] == $item['id'])
                                                    selected
                                                    @endif>{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">通知公众平台模板消息编号: TM00981</div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <input class="mui-switch mui-switch-animbg" id="withdraw_fail" type="checkbox"
                                           @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['withdraw_fail']))
                                           checked
                                           @endif
                                           onclick="message_default(this.id)"/>
                                </div>
                            </div>
                        @endif

                            @if(YunShop::notice()->getNotSend('withdraw_reject'))
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额提现驳回通知</label>
                                    <div class="col-sm-8 col-xs-12">
                                        <select name='yz_notice[withdraw_reject]' class='form-control diy-notice'>
                                            <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['withdraw_reject'])) value="{{$set['withdraw_reject']}}"
                                                    selected @else value=""
                                                    @endif
                                            >
                                                默认消息模板
                                            </option>
                                            @foreach ($temp_list as $item)
                                                <option value="{{$item['id']}}"
                                                        @if($set['withdraw_reject'] == $item['id'])
                                                        selected
                                                        @endif>{{$item['title']}}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block">通知公众平台模板消息编号: TM00982</div>
                                    </div>

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
                            var setting_name = "shop.notice";
                            var select_name = "select[name='yz_notice[" + name + "]']"
                            var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
                            var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
                            var postdata = {
                                notice_name: name,
                                setting_name: setting_name
                            };
                            if ($(id).is(':checked')) {
                                //开
                                $.post(url_open,postdata,function(data){
                                    if (data.result == 1) {
                                        $(select_name).find("option:selected").val(data.id)
                                        showPopover($(id),"开启成功")
                                    } else {
                                        showPopover($(id),"开启失败，请检查微信模版")
                                        $(id).attr("checked",false);
                                    }
                                }, "json");
                            } else {
                                //关
                                $.post(url_close,postdata,function(data){
                                    $(select_name).val('');
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
                        function noticeType(tpye) {
                            $("#type_id").val(tpye);
                        }
                        function select_member(o) {
                            if ($('.multi-item[openid="' + o.has_one_fans.openid + '"]').length > 0) {
                                return;
                            }
                            var html = '<div class="multi-item" openid="' + o.has_one_fans.openid + '">';
                            html += '<img class="img-responsive img-thumbnail" src="' + o.avatar + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
                            html += '<div class="img-nickname">' + o.nickname + '</div>';
                            html += '<input type="hidden" value="' + o.has_one_fans.openid + '" name="yz_notice[salers][' + o.uid + '][openid]">';
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
