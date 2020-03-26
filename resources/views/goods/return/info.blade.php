@extends('layouts.base')
@section('content')
@section('title', trans('退货地址模板详情'))
<style>
    select{width: 25%; height: 34px;}
    #saleravatar img{width: 200px; height: 200px;}
</style>
<div class="rightlist">
    <!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">退货地址设置</a></li>
        </ul>
    </div>
    <!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data" onsubmit='return formcheck()'>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>退货地址名称</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id='addressname' name="address[address_name]" class="form-control"
                               value="{{ $address->address_name }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>联系人</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id='contact' name="address[contact]" class="form-control"
                               value="{{ $address->contact }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>手机</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" id='mobile' name="address[mobile]" class="form-control"
                               value="{{ $address->mobile }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">电话</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="address[telephone]" class="form-control"
                               value="{{ $address->telephone }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>地址</label>
                    <div class="col-xs-6">
                        <input type="hidden" id="province_id" value="{{ $address->province_id?:0 }}"/>
                        <input type="hidden" id="city_id" value="{{ $address->city_id?:0 }}"/>
                        <input type="hidden" id="district_id" value="{{ $address->district_id?:0 }}"/>
                        <input type="hidden" id="street_id" value="{{ $address->street_id?:0 }}"/>
                        {!! app\common\helpers\AddressHelper::tplLinkedAddress(['address[province_id]','address[city_id]','address[district_id]','address[street_id]'], [])!!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>详细地址</label>
                    <div class="col-sm-9 col-xs-12">
                        <input class="form-control" id="address" type="text" name="address[address]" value="{{ $address->address }}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否为默认退货地址</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'>
                            <input type='radio' name='address[is_default]' id="isdefault1" value='1'
                                   @if ( $address->is_default == 1 )checked @endif /> 是
                        </label>
                        <label class='radio-inline'>
                            <input type='radio' name='address[is_default]' id="isdefault0" value='0'
                                   @if ( $address->is_default == 0 )checked @endif /> 否
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="submit" name="submit" value="提交" class="btn btn-success "
                               onclick="return formcheck()"/>
                        <input type="button" name="back" onclick='history.back()' value="返回列表"
                               class="btn btn-default back"/>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>
    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);

    function formcheck() {
        if ($("#addressname").val() == '') {
            Tip.focus("#addressname", "请填写配送方式名称!", "top");
            return false;
        }
        if ($("#contact").val() == '') {
            Tip.focus("#contact", "请填写联系人!", "top");
            return false;
        }
        if ($("#mobile").val() == '') {
            Tip.focus("#mobile", "请填写手机号!", "top");
            return false;
        }
        if ($("#address").val() == '') {
            Tip.focus("#address", "请填写详细地址!", "top");
            return false;
        }
        if ($("#sel-provance").val() == '') {
            Tip.focus("#address", "请填写地址", "top");
            return false;
        }
        if ($("#sel-city").val() == '') {
            Tip.focus("#address", "请填写地址", "top");
            return false;
        }
        if ($("#sel-area").val() == '') {
            Tip.focus("#address", "请填写地址", "top");
            return false;
        }
        if ($(':input[name="address[province_id]"]').val() == 0) {
            alert('请选择省份');
            return false;
        }
        if ($(':input[name="address[city_id]"]').val() == 0) {
            alert('请选择城市');
            return false;
        }
        if ($(':input[name="address[district_id]"]').val() == 0) {
            alert('请选择区域');
            return false;
        }
        if ($(':input[name="address[street_id]"]').val() == 0) {
            alert('请选择街道');
            return false;
        }
        return true;
    }
</script>


@endsection