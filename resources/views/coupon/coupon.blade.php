@extends('layouts.base')
@section('title', '编辑优惠券')
@section('content')

<div class="w1200 m0a">
    <div class="rightlist">
        <form action="" method='post' class='form-horizontal'>

            <div class='panel panel-default'>
                <ul class="add-shopnav" id="myTab">
                    <li class="active" ><a href="#tab_basic">基本信息</a></li>
                    <li><a href="#tab_coupon_type">优惠方式</a></li>
                    {{--<li><a href="#tab_desc">使用说明</a></li>--}}
                    {{--<li><a href="#tab_message">推送消息</a></li>--}}
                </ul>

                <div class='panel-body'>
                    <div class="tab-content">
                        <div class="tab-pane  active" id="tab_basic">@include('coupon.basic')</div>
                        <div class="tab-pane" id="tab_coupon_type">@include('coupon.coupontype')</div>
                        {{--<div class="tab-pane" id="tab_desc">@include('coupon.desc')</div>--}}
                        {{--<div class="tab-pane" id="tab_message">@include('coupon.message')</div>--}}
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"  />
                            <a href="{{yzWebUrl('coupon.coupon.index')}}"><input type="button" name="back" style='margin-left:10px;' value="返回列表" class="btn btn-default" /></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection('content')
@section('js')
    <script>
        $(function() {
            $("#chkoption").click(function() {
                var obj = $(this);
                if (obj.get(0).checked) {
                    $("#tboption").show();
                    $(".trp").hide();
                }
                else {
                    $("#tboption").hide();
                    $(".trp").show();
                }
            })
        });

        function index(e) {
            if ($('#param-itemsgoods-exchange').find('tr').length >= 1) {
                alert('兑换券不能添加多个商品');
                return false;
            }
            addParams(e)
        }

        function addParams(type) {
            var url = "{!! yzWebUrl('coupon.coupon.add-param')!!}" + '&type=' + type;
            $.ajax({
                "url": url,
                success: function (data) {
                    console.log(data);
                    $('#param-items' + type).append(data);
                }
            });
        }

        function addParam(type) {
            var url = "{!! yzWebUrl('coupon.coupon.add-param')!!}"+'&type='+type;
            $.ajax({
                "url": url,
                success: function(data) {
                    $('#param-items'+type).append(data);
                }
            });
        }
        function deleteParam(o) {
            $(o).parent().parent().remove();
        }
        function saveadd(o) {
            $(o).parent().parent().remove();
        }

        function showbacktype(type){

            $('.backtype').hide();
            $('.backtype' + type).show();
        }

        function showusetype(type){
            $('.usetype').hide();
            $('.usetype' + type).show();
        }
        $(function(){
            require(['bootstrap'], function () {
                $('#myTab a').click(function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                })
            });
            $('form').submit(function(){

                if($('#couponname').isEmpty()){
                    Tip.focus($(':input[name=couponname]'),'请输入优惠券名称!');
                    return false;
                }
                var coupon_method = $('#couponmethod:checked').val();
                if(coupon_method=='1'){
                    if($(':input[name=deduct]').isEmpty()){
                        Tip.focus($(':input[name=deduct]'),'请输入立减多少!');
                        return false;
                    }
                }else if(coupon_method=='2'){
                    if($(':input[name=discount]').isEmpty()){
                        Tip.focus($(':input[name=discount]'),'请输入折扣多少!');
                        return false;
                    }
                }
                return true;
            })
        })

        {{--搜索分类--}}
        function search_categorys() {
            if ($.trim($('#search-kwd-categorys').val()) == '') {
                Tip.focus('#search-kwd-categorys', '请输入关键词');
                return;
            }
            $("#module-menus-categorys").html("正在搜索....");
            $.get('{!! yzWebUrl('goods.category.get-search-categorys') !!}', {
                    keyword: $.trim($('#search-kwd-categorys').val())
                }, function (dat) {
                    $('#module-menus-categorys').html(dat);
                }
            );
        }
        function select_category(o) {
            $(".focuscategory:last input[data-name=categoryids]").val(o.id);
            $(".focuscategory:last input[data-name=categorynames]").val(o.name);
            $(".focuscategory").removeClass("focuscategory");
            $("#modal-module-menus-categorys .close").click();
        }

        {{--搜索商品--}}
        function search_goods() {
            if ($.trim($('#search-kwd-goods').val()) == '') {
                Tip.focus('#search-kwd-goods', '请输入关键词');
                return;
            }
            $("#module-menus-goods").html("正在搜索....");
            $.get('{!! yzWebUrl('goods.goods.get-search-goods') !!}', {
                    keyword: $.trim($('#search-kwd-goods').val())
                }, function (dat) {
                    $('#module-menus-goods').html(dat);
                }
            );
        }
        function select_good(o) {
            console.log(o);
            $(".focusgood:last input[data-name=goodsids]").val(o.id);
            $(".focusgood:last input[data-name=goodsnames]").val(o.title);
            $(".focusgood").removeClass("focusgood");
            $("#modal-module-menus-goods .close").click();
        }

        {{--搜索门店--}}
        function search_store() {
            if ($.trim($('#search-kwd-store').val()) == '') {
                Tip.focus('#search-kwd-store', '请输入关键词');
                return;
            }
            $("#module-menus-store").html("正在搜索....");
            $.get('{!! yzWebUrl('goods.goods.get-search-store') !!}', {
                    keyword: $.trim($('#search-kwd-store').val())
                }, function (dat) {
                    $('#module-menus-store').html(dat);
                }
            );
        }
        {{--搜索门店--}}
        function search_hotel() {
            if ($.trim($('#search-kwd-hotel').val()) == '') {
                Tip.focus('#search-kwd-hotel', '请输入关键词');
                return;
            }
            $("#module-menus-hotel").html("正在搜索....");
            $.get('{!! yzWebUrl('goods.goods.get-search-hotel') !!}', {
                    keyword: $.trim($('#search-kwd-hotel').val())
                }, function (dat) {
                    $('#module-menus-hotel').html(dat);
                }
            );
        }
        function select_store(o) {
            console.log(o);
            $(".focusstore:last input[data-name=storeids]").val(o.id);
            $(".focusstore:last input[data-name=storenames]").val(o.store_name);
            $(".focusstore").removeClass("focusstore");
            $("#modal-module-menus-store .close").click();
        }
        function select_hotel(o) {
            console.log(o);
            $(".focushotel:last input[data-name=hotelids]").val(o.id);
            $(".focushotel:last input[data-name=hotelnames]").val(o.hotel_name);
            $(".focushotel").removeClass("focushotel");
            $("#modal-module-menus-hotel .close").click();
        }

        {{--搜索商品--}}
        function search_exchange_goods() {
            if ($.trim($('#search-kwd-goods-exchange').val()) == '') {
                Tip.focus('#search-kwd-goods-exchange', '请输入关键词');
                return;
            }
            $("#module-menus-goods").html("正在搜索....");
            $.get('{!! yzWebUrl('goods.goods.get-search-goods') !!}', {
                    keyword: $.trim($('#search-kwd-goods-exchange').val()),exchange:1
                }, function (dat) {
                    $('#module-menus-goods-exchange').html(dat);
                }
            );
        }

        function select_goods_exchange(o) {
            $(".focusgood:last input[data-name=goodsid]").val(o.id);
            $(".focusgood:last input[data-name=goodsname]").val(o.title);
            $(".focusgood").removeClass("focusgood");
            $("#modal-module-menus-goods-exchange .close").click();
        }
    </script>
@endsection('js')
