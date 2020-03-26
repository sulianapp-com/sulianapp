@extends('layouts.base')
@section('content')
@section('title', trans('优惠券设置'))
<div class="w1200 m0a">
    <div class="rightlist">

        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="shopform">
            <div class="panel panel-default">
                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券到期消息通知</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="table-responsive ">
                                <div class="input-group">
                                    <div class="input-group-addon">到期前</div>
                                    <input type="text" name="coupon[delayed]" class="form-control" value="{{$set['delayed']}}"/>
                                    <div class="input-group-addon">天发送通知</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">提醒时间</label>
                        <div class="col-sm-6 col-xs-6">
                            <div class='input-group'>
                                <label class="radio-inline">
                                    <input type="radio" name="coupon[send_times]" value="0"
                                           @if($set['send_times'] == 0) checked="checked" @endif />
                                    每天
                                    <select name='coupon[every_day]' class='form-control'>
                                        @foreach($hourData as $hour)
                                            <option value='{{$hour['key']}}'
                                                    @if($set['every_day'] == $hour['key']) selected @endif>{{$hour['name']}}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券过期提醒</label>
                            <div class="col-sm-8 col-xs-12">
                                <select name='coupon[expire]' class='form-control diy-notice'>
                                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['expire'])) value="{{$set['expire']}}"
                                            selected @else value="" @endif>
                                        默认消息模板
                                    </option>
                                    @foreach ($temp_list as $item)
                                        <option value="{{$item['id']}}"
                                                @if($set['expire'] == $item['id'])
                                                selected
                                                @endif>{{$item['title']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2 col-xs-6">
                                <input class="mui-switch mui-switch-animbg" id="expire" type="checkbox"
                                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['expire']))
                                       checked
                                       @endif
                                       onclick="message_default(this.id)"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"
                                   onclick="return formcheck()"/>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<script>
    function message_default(name) {
        var id = "#" + name;
        var setting_name = "shop.coupon";
        var select_name = "select[name='coupon[" + name + "]']"
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
<script>
    $('.diy-notice').select2();
</script>
@endsection
