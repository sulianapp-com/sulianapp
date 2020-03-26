
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用条件 - 订单金额</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="coupon[enough]" class="form-control" value="{{isset($coupon['enough']) ? $coupon['enough'] : 0}}"  />
        <span class='help-block'>消费满多少金额才可以使用该优惠券 (设置为 0 则不限制消费金额)</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用条件 - 会员等级</label>
    <div class="col-sm-9 col-xs-12">
        <select name="coupon[level_limit]" class="form-control" id="value_2" >
            <option value="-1" selected>所有会员</option>
            @foreach($memberlevels as $v)
                <option value="{{$v['id']}}" @if($coupon['level_limit']==$v['id']) selected @endif>{{$v['level_name']}}(及以上等级可以领取)</option>
            @endforeach
        </select>
        <span class='help-block'>选择"所有会员"表示商城的所有会员,包括没有划分等级的; <br>例如: 选择等级 3,表示包括 3 以及大于等级 3 的会员都可领取,即等级 3, 4, 5...都可以领取.</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用时间限制</label>
    <div class="col-sm-9 form-inline">
        <div class='input-group form-group col-sm-6'>
            <span class='input-group-addon'>
                 <label class="radio-inline" style='margin-top:-5px;' >
                     <input type="radio" name="coupon[time_limit]" value="0" checked>获得后
                 </label>
            </span>
            <input type='text' class='form-control' name='coupon[time_days]' value="{{isset($coupon['time_days']) ? $coupon['time_days'] : 1}}" />
            <span class='input-group-addon'>天内有效(0 为不限时间使用)</span>
        </div>
        <br>
        <div class='input-group form-group col-sm-3'>
            <span class='input-group-addon'>
                 <label class="radio-inline" style='margin-top:-5px;' >
                     <input type="radio" name="coupon[time_limit]" value="1" @if ($coupon['time_limit']==1) checked  @endif>日期
                 </label>
            </span>
            {!! tpl_form_field_daterange('time', array(
                    'starttime'=>date('Y-m-d', !empty($timestart) ? $timestart : strtotime('today')),
                    'endtime'=>date('Y-m-d', !empty($timeend) ? $timeend : strtotime('+7 days')))
            ) !!}
            <span class='input-group-addon'>内有效</span>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">使用方式</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type="radio" name="coupon[is_complex]" value="0" @if($coupon['is_complex']!=='' && $coupon['is_complex']==0) checked @endif/>单张使用
        </label>
        <label class='radio-inline'>
            <input type="radio" name="coupon[is_complex]" value="1" @if($coupon['is_complex']==1) checked @endif/>多张一起使用
        </label>
        <span class='help-block'>如选择单张使用，则一笔订单只能使用一张该类型的优惠券；
选择多张一起使用，则满足使用的金额就可以， 比如我有300-50优惠券3张，下单金额满900元，可以用三张，下单金额满600元可以用2张，下单金额满300元可以用一张</span>
    </div>
</div>

@include('coupon.consume')
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">适用范围</label>
    <div class="col-sm-9 col-xs-12">
        <label class="radio-inline"><input type="radio" name="coupon[use_type]" onclick='showusetype(0)' value="0" checked>全类适用</label>
        <label class="radio-inline"><input type="radio" name="coupon[use_type]" onclick='showusetype(1)' value="1" @if($usetype==1)checked @endif>指定商品分类</label>
        <label class="radio-inline"><input type="radio" name="coupon[use_type]" onclick='showusetype(2)' value="2" @if($usetype==2)checked @endif>指定商品</label>
        <label class="radio-inline"><input type="radio" name="coupon[use_type]" onclick='showusetype(4)' value="4" @if($coupon['use_type']==4)checked @endif>指定门店</label>
        <label class="radio-inline"  @if(!$hotel_is_open) style="display: none" @endif><input type="radio" name="coupon[use_type]" onclick='showusetype(7)' value="7" @if($coupon['use_type']==7)checked @endif>指定酒店</label>
        <label class="radio-inline"><input type="radio" name="coupon[use_type]" onclick='showusetype(8)' value="8" @if($coupon['use_type']==8)checked @endif>兑换券</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>

    {{--隐藏窗口 - 适用范围:商城通用--}}
    <div class="col-sm-7 usetype usetype0"  @if($usetype!=0)style='display:none' @endif>
        <div class='input-group'>
            <span class='help-block'>如选择此项,则支持商城所有商品使用!</span>
        </div>
    </div>
    {{--隐藏窗口 - 适用范围:指定分类--}}
    <div class="col-sm-7 usetype usetype1"  @if($usetype!=1)style='display:none' @endif>
        <div class='input-group'>
            <div id="category" >
                <table class="table">
                    <tbody id="param-itemscategory">
                    @if($coupon['category_ids'])
                    @foreach($coupon['category_ids'] as $k=>$v)
                        <tr>
                            <td>
                                <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                            </td>
                            <td  colspan="2">
                                <input id="categoryid" type="hidden" class="form-control" name="category_ids[]" data-id="{{$v}}" data-name="categoryids"  value="{{$v}}" style="width:200px;float:left"  />
                                <input id="categoryname" class="form-control" type="text" name="category_names[]" data-id="{{$v}}" data-name="categorynames" value="{{$coupon['categorynames'][$k]}}" style="width:200px;float:left" readonly="true">
                                <span class="input-group-btn">
                                    <button class="btn btn-default nav-link" type="button" data-id="{{$v}}" onclick="$('#modal-module-menus-categorys').modal();$(this).parent().parent().addClass('focuscategory')" >选择分类</button>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_category' onclick="addParam('category')"
                               style="margin-top:10px;" class="btn btn-primary"  title="添加分类"><i class='fa fa-plus'></i> 添加分类</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{--隐藏窗口 - 适用范围:指定商品--}}
    <div class="col-sm-7 usetype usetype2"  @if($usetype!=2)style='display:none' @endif>
        <div class='input-group'>

            <div id="goods">
                <table class="table">
                    <tbody id="param-itemsgoods">
                    @if ($coupon['goods_ids'] and $usetype == 2)
                    @foreach ($coupon['goods_ids'] as $k=>$v)
                        <tr>
                            <td>
                                <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                            </td>
                            <td  colspan="2">
                                <input id="goodid" type="hidden" class="form-control" name="goods_ids[]" data-id="{{$v}}" data-name="goodsids"  value="{{$v}}" style="width:200px;float:left"  />
                                <input id="goodname" class="form-control" type="text" name="goods_names[]" data-id="{{$v}}" data-name="goodsnames" value="{{$coupon['goods_names'][$k]}}" style="width:200px;float:left" readonly="true">
                                <span class="input-group-btn">
                                    <button class="btn btn-default nav-link-goods" type="button" data-id="{{$v}}" onclick="$('#modal-module-menus-goods').modal();$(this).parent().parent().addClass('focusgood')">选择商品</button>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>

                    <tbody>
                    <tr>

                        <td colspan="3">
                            <a href="javascript:;" id='add-param_goods' onclick="addParam('goods' )"
                               style="margin-top:10px;" class="btn btn-primary" title="添加商品"><i class='fa fa-plus'></i> 添加商品</a>
                        </td>
                    </tr>
                    </tbody>

                </table>

            </div>
        </div>

    </div>　

    <div class="col-sm-7 usetype usetype8" @if($usetype!=8)style='display:none' @endif>
        <div class='input-group'>
            <div id="goods-exchange">
                <table class="table">
                    <tbody id="param-itemsgoods-exchange">
                    @if ($coupon['goods_ids'] and $usetype == 8)
                        @foreach ($coupon['goods_ids'] as $k=>$v)
                            <tr>
                                <td>
                                    <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"
                                       title="删除"><i class='fa fa-times'></i></a>
                                </td>
                                <td colspan="2">
                                    <input id="goodid" type="hidden" class="form-control" name="goods_id[]"
                                           data-id="{{$v}}" data-name="goodsid" value="{{$v}}"
                                           style="width:200px;float:left"/>
                                    <input id="goodname" class="form-control" type="text" name="goods_name[]"
                                           data-id="{{$v}}" data-name="goodsname"
                                           value="{{$coupon['goods_names'][$k]}}" style="width:200px;float:left"
                                           readonly="true">
                                    <span class="input-group-btn">
                                    <button class="btn btn-default nav-link-goods" type="button" data-id="{{$v}}"
                                            onclick="$('#modal-module-menus-goods-exchange').modal();$(this).parent().parent().addClass('focusgood')">选择兑换商品</button>
                                </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-params_goods' onclick="index('goods-exchange')"
                               style="margin-top:10px;" class="btn btn-primary" title="添加兑换商品"><i
                                        class='fa fa-plus'></i> 添加兑换商品</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    　
    {{--隐藏窗口 - 适用范围:指定门店--}}
    <div class="col-sm-7 usetype usetype4"  @if($coupon['use_type']!=4)style='display:none' @endif>
        <div class='input-group'>

            <div id="store">
                <table class="table">
                    <tbody id="param-itemsstore">
                    @if ($coupon['storeids'])
                        @foreach ($coupon['storeids'] as $k=>$v)
                            <tr>
                                <td>
                                    <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                                </td>
                                <td  colspan="2">
                                    <input id="storeid" type="hidden" class="form-control" name="store_ids[]" data-id="{{$v}}" data-name="storeids"  value="{{$v}}" style="width:200px;float:left"  />
                                    <input id="storename" class="form-control" type="text" name="store_names[]" data-id="{{$v}}" data-name="storenames" value="{{$coupon['storenames'][$k]}}" style="width:200px;float:left" readonly="true">
                                    <span class="input-group-btn">
                                    <button class="btn btn-default nav-link-store" type="button" data-id="{{$v}}" onclick="$('#modal-module-menus-store').modal();$(this).parent().parent().addClass('focusstore')">选择门店</button>
                                </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>

                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_store' onclick="addParam('store')"
                               style="margin-top:10px;" class="btn btn-primary" title="添加门店"><i class='fa fa-plus'></i> 添加门店</a>
                        </td>
                    </tr>
                    </tbody>

                </table>

            </div>
        </div>

    </div>
    {{--隐藏窗口 - 适用范围:指定酒店--}}
    <div class="col-sm-7 usetype usetype7"  @if($coupon['use_type']!=7 || !$hotel_is_open)style='display:none' @endif>
        <div class='input-group'>
            <div id="hotel">
                <table class="table">
                    <tbody id="param-itemshotel">
                    @if ($hotels)
                        @foreach ($hotels as $v)
                            <tr>
                                <td>
                                    <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
                                </td>
                                <td  colspan="2">
                                    <input id="hotelid" type="hidden" class="form-control" name="hotel_ids[]" data-id="{{$v->id}}" data-name="hotelids"  value="{{$v->id}}" style="width:200px;float:left"  />
                                    <input id="hotelname" class="form-control" type="text" name="hotel_names[]" data-id="{{$v->id}}" data-name="hotelnames" value="{{$v->hotel_name}}" style="width:200px;float:left" readonly="true">
                                    <span class="input-group-btn">
                                    <button class="btn btn-default nav-link-hotel" type="button" data-id="{{$v->id}}" onclick="$('#modal-module-menus-hotel').modal();$(this).parent().parent().addClass('focushotel')">选择酒店</button>
                                </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>

                    <tbody>
                    <tr>
                        <td colspan="3">
                            <a href="javascript:;" id='add-param_hotel' onclick="addParam('hotel')"
                               style="margin-top:10px;" class="btn btn-primary" title="添加酒店"><i class='fa fa-plus'></i> 添加酒店</a>
                        </td>
                    </tr>
                    </tbody>

                </table>

            </div>
        </div>

    </div>

</div>

<div id="goods" style="display: none">

</div>

<div id="modal-module-menus-categorys" class="modal fade" tabindex="-1"> {{--搜索分类的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择分类</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-categorys" placeholder="请输入分类名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_categorys();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-categorys" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

<div id="modal-module-menus-goods" class="modal fade" tabindex="-1"> {{--搜索商品的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择商品</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-goods" placeholder="请输入商品名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_goods();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-goods" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

{{--搜索兑换中心指定商品的弹窗--}}
<div id="modal-module-menus-goods-exchange" class="modal fade" tabindex="-1">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择兑换商品</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-goods-exchange" placeholder="请输入兑换商品名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_exchange_goods();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-goods-exchange" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

<div id="modal-module-menus-store" class="modal fade" tabindex="-1"> {{--搜索门店的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择门店</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-store" placeholder="请输入门店名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_store();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-store" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

<div id="modal-module-menus-hotel" class="modal fade" tabindex="-1"> {{--搜索酒店的弹窗--}}
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择酒店</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-hotel" placeholder="请输入酒店名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_hotel();">搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-hotel" style="padding-top:5px;"></div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default"
                                         data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否可领取</label>
    <div class="col-sm-9 col-xs-12" >
        <label class="radio-inline">
            <input type="radio" name="coupon[get_type]" value="1" checked onclick="$('.gettype').show()" /> 可以
        </label>
        <label class="radio-inline">
            <input type="radio" name="coupon[get_type]" value="0" @if($coupon['get_type'] === 0)checked="true" @endif onclick="$('.gettype').hide()"/> 不可以
        </label>
        <span class='help-block'>是否可以在领券中心领取 (或者只能手动发放)</span>

    </div>
</div>

<div class="form-group gettype" @if($coupon['get_type'] === 0) style='display:none' @endif>
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-9 form-inline">
        <div class="input-group form-group col-sm-1">
            <span class="input-group-addon">每人限领张数:</span>
            <input type='text' class='form-control' value="{{isset($coupon['get_max']) ? $coupon['get_max'] : 1}}" name='coupon[get_max]' style="width: 80px" />
        </div>
        {{--<div class="input-group form-group col-sm-1">--}}
            {{--<span class="input-group-addon">消耗积分:</span>--}}
            {{--<input style="width: 80px"  type='text' class='form-control' value="{{isset($coupon['credit']) ? $coupon['credit'] : 0}}" name='coupon[credit]'/>--}}
        {{--</div>--}}
        <span class="help-block">每人限领数量 (-1为不限制数量).</span>
    </div>

</div>
　
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发放总数</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="coupon[total]" class="form-control" value="{{isset($coupon['total']) ? $coupon['total'] : 0}}"  />
        <span class='help-block' >优惠券总数量，没有则不能领取或发放, -1 为不限制数量</span>
    </div>
</div>
　