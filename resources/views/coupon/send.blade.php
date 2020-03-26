@extends('layouts.base')

@section('content')
<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">

    <div class='panel panel-default'>

        <div class='panel-heading'>
            发放优惠券
        </div>
        <div class='panel-body'>
            {{--<div class="form-group">--}}
                {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 选择优惠券</label>--}}
                {{--<div class="col-sm-5">--}}
                    {{--<input type='hidden' id='couponid' name='couponid' value="{{$coupon['id']}}"/>--}}
                    {{--<div class='input-group'>--}}
                        {{--<input type="text" name="coupondec" maxlength="30" id="coupon" class="form-control" readonly value="" placeholder="[优惠券] 优惠券名称" />--}}
                        {{--<div class='input-group-btn'>--}}
                            {{--<button class="btn btn-default" type="button" onclick="$('#modal-module-menus-coupon').modal();">选择优惠券</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">发送张数</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" id="send_total" name="send_total" class="form-control" placeholder="请输入数字, 不小于 1"  />
                    <span style="color: red">该处发放张数为发放个单个用户的张数</span>
                </div>
            </div>
        </div>

        <div class='panel-heading'>
            发送对象
        </div>
        <div class='panel-body'>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >发送类型</label>
                <div class="col-sm-9 col-xs-12">
                    <label class="radio-inline"><input type="radio" name="sendtype" value="1" checked/> 按会员ID发送</label>
                    <label class="radio-inline"><input type="radio" name="sendtype" value="2" @if($sendtype == '2') checked @endif/> 按用户等级发送</label>
                    <label class="radio-inline"><input type="radio" name="sendtype" value="3" @if($sendtype == '3') checked @endif/> 按用户分组发送</label>
                    <label class="radio-inline"><input type="radio" name="sendtype" value="4" @if($sendtype == '4') checked @endif/> 发送给全部用户</label>
                </div>
            </div>
            <div class="form-group choose choose_1">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >会员ID</label>
                <div class="col-sm-9 col-xs-12">
                    <textarea name="send_memberid" class="form-control" style="height:250px;" placeholder="请用&quot;半角逗号&quot;隔开会员ID, 比如 1,2,3,注意不能有空格" id="value_1"></textarea>
                </div>
            </div>
            <div class="form-group choose choose_2" style='display: none' >
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >选择会员等级</label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <select name="send_level" class="form-control" id="value_2" >
                        <option></option>
                        @foreach($memberLevels as $v)
                        <option value="{{$v['level']}}">{{$v['level_name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group choose choose_3" style='display:none '>
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >选择会员分组</label>
                <div class="col-sm-8 col-lg-9 col-xs-12">
                    <select name="send_group" class="form-control"  id="value_3">
                        <option></option>
                        @foreach($memberGroups as $v)
                        <option value="{{$v['id']}}">{{$v['group_name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" ></label>
            <div class="col-sm-9 col-xs-12">
                <div class="help-block">
                    <input type="submit" name="submit" value="确认发放" class="btn btn-primary col-lg-4"/>
                </div>
            </div>
        </div>

    </div>
</form>


{{--搜索优惠券弹窗--}}
{{--<div id="modal-module-menus-coupon"  class="modal fade" tabindex="-1">--}}
    {{--<div class="modal-dialog" style='width: 920px;'>--}}
        {{--<div class="modal-content">--}}
            {{--<div class="modal-header">--}}
                {{--<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择优惠券</h3>--}}
            {{--</div>--}}
            {{--<div class="modal-body" >--}}
                {{--<div class="row">--}}
                    {{--<div class="input-group">--}}
                        {{--<input type="text" class="form-control" name="keyword" value="" id="search-kwd-coupons" placeholder="请输入优惠券名称" />--}}
                        {{--<span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_coupons();">搜索</button></span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div id="module-menus-coupon" style="padding-top:5px;">--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

<script>
    //验证是否是整数
    function validateInt(value){
        if(value == parseInt(value)){
            return true;
        } else{
            return false;
        }
    }

    function search_coupons() {
        $("#module-menus-coupon").html("正在搜索....");
        $.get('{!! yzWebUrl('coupon.coupon.get-search-coupons') !!}', {
            keyword: $.trim($('#search-kwd-coupons').val())
        }, function (dat) {
            $('#module-menus-coupon').html(dat);
        });
    }

    function select_coupon(o) {
        $("#couponid").val(o.id);
        $("#coupon").val('[' + o.id + "] " + o.name);
        $(".close").click();
    }

    $(function () {
        $(':radio[name=sendtype]').click(function () {
            var v = $(this).val();
            $(".choose").hide();
            $(".choose_" + v).show();
        })

        $('form').submit(function () {
            var couponid = $('#couponid').val();
            if (couponid == '') {
                Tip.show($('#coupon'), '请选择要发放的优惠券!');
                return false;
            }
            var send_total = $('#send_total').val();
            if (!validateInt(send_total)) {
                Tip.select($('#send_total'), '请输入整数!');
                return false;
            }
            send_total = parseInt(send_total);
            if (send_total <= 0) {
                Tip.select($('#send_total'), '最少发放一张!');
                return false;
            }
            var c = $('input[name=sendtype]:checked').val();
            var v = $('#value_1').val().trim();
            if (c == 1 && v == '') {
                Tip.show(($('#value_1')),'请输入要发放的用户 Member ID !');
                return false;
            }
            return true;
        });
    });
</script>

@endsection('content')