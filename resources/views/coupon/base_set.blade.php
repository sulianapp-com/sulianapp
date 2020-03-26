@extends('layouts.base')
@section('title', '优惠券设置')
@section('content')

    <div class="rightlist">
        @include('layouts.tabs')

        <form action="{{ yzWebUrl('coupon.base-set.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>


                <div class='panel-heading'>基础设置</div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券使用限制：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[is_singleton]" value="1" @if ($coupon['is_singleton'] == 1) checked="checked" @endif />
                                单张
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[is_singleton]" value="0" @if ($coupon['is_singleton'] == 0) checked="checked" @endif />
                                多张
                            </label>
                            <div class="help-block">
                                选中单张时每个订单最多只能使用一张优惠券
                            </div>
                        </div>
                    </div>
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券转让：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[transfer]" value="1" @if ($coupon['transfer'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[transfer]" value="0" @if ($coupon['transfer'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block">
                                优惠券转让：会员之间可以转让自己拥有的优惠券。
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">抵扣奖励积分：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[award_point]" value="1" @if ($coupon['award_point'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[award_point]" value="0" @if ($coupon['award_point'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block">
                                优惠券抵扣金额奖励等值积分，如优惠券抵扣 10元则奖励 10积分。
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券返还：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[order_close_return]" value="1" @if ($coupon['order_close_return'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[order_close_return]" value="0" @if ($coupon['order_close_return'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block" style="width: 450px">
                                开启优惠券返还：未付款订单、退款订单关闭订单后，使用的优惠券返还到会员账户。
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">兑换中心：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[exchange_center]" value="1" @if ($coupon['exchange_center'] == 1) checked="checked" @endif />
                                开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[exchange_center]" value="0" @if ($coupon['exchange_center'] == 0) checked="checked" @endif />
                                关闭
                            </label>
                            <div class="help-block" style="width: 450px">
                                兑换中心开关
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知设置：</label>
                        <div class="col-sm-4 col-xs-6">
                            <select name='coupon[coupon_notice]' class='form-control diy-notice'>
                                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($coupon['coupon_notice'])) value="{{$coupon['coupon_notice']}}"
                                        selected @else value="" @endif>
                                    默认消息模板
                                </option>
                                @foreach ($temp_list as $item)
                                    <option value="{{$item['id']}}"
                                            @if($coupon['coupon_notice'] == $item['id'])
                                            selected
                                            @endif>{{$item['title']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2 col-xs-6">
                            <input class="mui-switch mui-switch-animbg" id="coupon_notice" type="checkbox"
                                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($coupon['coupon_notice']))
                                   checked
                                   @endif
                                   onclick="message_default(this.id)"/>
                        </div>
                    </div>
                </div>


                <div class='panel-heading'>购物分享设置</div>
                <div class='panel-body'>
                    {{--<div class="form-group">--}}
                        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">购买商品分享优惠券：</label>--}}
                        {{--<div class="col-sm-4 col-xs-6">--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="radio" name="coupon[shopping_share][share_open]" value="1" @if ($coupon['shopping_share']['share_open'] == 1) checked="checked" @endif />--}}
                                {{--开启--}}
                            {{--</label>--}}
                            {{--<label class="radio-inline">--}}
                                {{--<input type="radio" name="coupon[shopping_share][share_open]" value="0" @if ($coupon['shopping_share']['share_open'] == 0) checked="checked" @endif />--}}
                                {{--关闭--}}
                            {{--</label>--}}
                            {{--<div class="help-block">--}}
                                {{--会员购买指定商品，获得优惠券分享资格--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享限制：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[shopping_share][share_limit]" value="1" @if ($coupon['shopping_share']['share_limit'] == 1) checked="checked" @endif />
                                是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[shopping_share][share_limit]" value="0" @if ($coupon['shopping_share']['share_limit'] == 0) checked="checked" @endif />
                                否
                            </label>
                            <div class="help-block">
                                是否限制为拥有推广资格的会员才可以分享
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">领取限制：</label>
                        <div class="col-sm-4 col-xs-6">
                            <label class="radio-inline">
                                <input type="radio" name="coupon[shopping_share][receive_limit]" value="1" @if ($coupon['shopping_share']['receive_limit'] == 1) checked="checked" @endif />
                                是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="coupon[shopping_share][receive_limit]" value="0" @if ($coupon['shopping_share']['receive_limit'] == 0) checked="checked" @endif />
                                否
                            </label>
                            <div class="help-block">
                                分享者是否可以领取
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享Banner图</label>
                        <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('coupon[shopping_share][banner]', $coupon['shopping_share']['banner']) !!}
                            {{--<span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>--}}
                            {{--@if (!empty($coupon['shopping_share']['banner']))
                                <a href='{{tomedia($coupon['shopping_share']['banner'])}}' target='_blank'>
                                    <img src="{{tomedia($coupon['shopping_share']['banner'])}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                </a>
                            @endif--}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="coupon[shopping_share][share_title]" class="form-control" value="{{$coupon['shopping_share']['share_title']}}">
                            <span class="help-block">如果不填写，默认为商城名称</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea name="coupon[shopping_share][share_desc]" class="form-control">{{$coupon['shopping_share']['share_desc']}}</textarea>
                            <span class="help-block">如果不填写，默认为空</span>
                        </div>
                    </div>

                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存设置" class="btn btn-primary" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <script>
        function message_default(name) {
            var id = "#" + name;
            var setting_name = "coupon." + name;
            var select_name = "select[name='coupon[" + name + "]']"
            var url_open = "{!! yzWebUrl('setting.default-notice.store') !!}"
            var url_close = "{!! yzWebUrl('setting.default-notice.storeCancel') !!}"
            var postdata = {
                notice_name: name,
                setting_name: setting_name
            };
            if ($(id).is(':checked')) {
                //开
                $.post(url_open,postdata,function(data){
                    if (data) {
                        if (data.result == 1) {
                            $(select_name).find("option:selected").val(data.id)
                            showPopover($(id),"开启成功")
                        } else {
                            showPopover($(id),"开启失败，请检查微信模版")
                            $(id).attr("checked",false);
                        }
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
    <script>
        $('.diy-notice').select2();
    </script>


@endsection

