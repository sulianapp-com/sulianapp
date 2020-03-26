<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>

{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">余额抵扣</label>--}}
    {{--<div class="col-xs-12 col-sm-9 col-md-10">--}}
        {{--<div class='input-group'>--}}
            {{--<span class="input-group-addon">最多抵扣</span>--}}
            {{--<input type="text" name="widgets[sale][max_balance_deduct]" value="{{ $item->max_balance_deduct }}"--}}
                   {{--class="form-control"/>--}}
            {{--<span class="input-group-addon">元</span>--}}
        {{--</div>--}}
        {{--<span class="help-block">如果设置0，则支持全额抵扣，设置-1 不支持余额抵扣</span>--}}

    {{--</div>--}}
{{--</div>--}}

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">单品满件包邮</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div class='input-group col-md-3'>
            <span class="input-group-addon">满</span>
            <input type="text" name="widgets[sale][ed_num]" value="{{ $item->ed_num }}" class="form-control"/>
            <span class="input-group-addon">件</span>
        </div>
        <span class="help-block">设置为空使用全局设置。设置0，则不支持满件包邮</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">单品满额包邮</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div class='input-group col-md-3'>
            <span class="input-group-addon">满</span>
            <input type="text" name="widgets[sale][ed_money]" value="{{ $item->ed_money }}" class="form-control"/>
            <span class="input-group-addon">元</span>
        </div>
        <span class="help-block">设置为空使用全局设置。设置0，则不支持满额包邮</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">单品满额立减</label>
    <div class="col-sm-9 col-xs-12">
        <div class="table-responsive ">
            <div class="return-queue">
                <div class="input-group col-md-2">
                    <div class="input-group-addon">满</div>
                    <input type="text" name="widgets[sale][ed_full]" class="form-control" value="{{ $item->ed_full }}" style="width: 136px;"/>
                    <div class="input-group-addon">元</div>
                </div>
                <div class="input-group col-md-2">
                    <div class="input-group-addon">减</div>
                    <input type="text" name="widgets[sale][ed_reduction]" class="form-control" value="{{ $item->ed_reduction }}" style="width: 136px;"/>
                    <div class="input-group-addon">元</div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送余额</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div class='input-group col-md-3'>
            <input type="text" name="widgets[sale][award_balance]" value="{{ $item->award_balance ?: 0 }}" class="form-control"/>
            <span class="input-group-addon">余额</span>
        </div>
        <span class="help-block">
            如果设置0，则不赠送<br>
            如: 购买2件，设置10 余额, 不管成交价格是多少， 则购买后获得20余额<br>
            如: 购买2件，设置10%余额, 成交价格2 * 200= 400， 则购买后获得 40 余额（400*10%）</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">赠送积分</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div class='input-group col-md-3'>
            <input type="text" name="widgets[sale][point]" value="{{ $item->point }}" class="form-control"/>
            <span class="input-group-addon">积分</span>
        </div>

        <span class="help-block">
            如果设置空，则走积分统一设置<br>
            如果设置0，则不赠送<br>
        如: 购买2件，设置10 积分, 不管成交价格是多少， 则购买后获得20积分<br>
            如: 购买2件，设置10%积分, 成交价格2 * 200= 400， 则购买后获得 40 积分（400*10%）</span>

        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[sale][point_type]' value='0' @if(!$item->point_type) checked @endif/>
                订单完成立即赠送
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[sale][point_type]' value='1' @if( $item->point_type == 1) checked @endif/>
                每月1号赠送
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div id="widgets_sale_point" class='input-group col-md-3' @if(!$item->point_type) style="display:none" @endif>
        <span class="input-group-addon">每月赠送</span>
        <input onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''" type="text" name="widgets[sale][max_once_point]" value="{{ $item->max_once_point }}"
               class="form-control"/>
        <span class="input-group-addon">积分</span>
    </div>
</div>

{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">红包</label>--}}
    {{--<div class="col-xs-12 col-sm-9 col-md-10">--}}
        {{--<div class='input-group'>--}}
            {{--<input type="text" name="widgets[sale][bonus]" value="{{ $item->bonus }}" class="form-control"/>--}}
            {{--<span class="input-group-addon">元</span>--}}
        {{--</div>--}}
        {{--<span class="help-block">如果设置0，则不发放红包</span>--}}
    {{--</div>--}}
{{--</div>--}}


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分抵扣</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div class='input-group col-md-3'>
            <span class="input-group-addon">最多抵扣</span>
            <input type="text" name="widgets[sale][max_point_deduct]" value="{{ $item->max_point_deduct }}"
                   class="form-control"/>
            <span class="input-group-addon">元</span>
        </div>
        <div class='input-group col-md-3'>
            <span class="input-group-addon">最少抵扣</span>
            <input type="text" name="widgets[sale][min_point_deduct]" value="{{ $item->min_point_deduct }}"
                   class="form-control"/>
            <span class="input-group-addon">元</span>
        </div>
        <span class="help-block">抵扣金额不能大于商品现价<br>如果设置空，则采用积分统一设置<br>如果设置0，则不支持积分抵扣</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分全额抵扣</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[sale][has_all_point_deduct]' value='1' @if($item->has_all_point_deduct == '1') checked @endif/>
            开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[sale][has_all_point_deduct]' value='0' @if( $item->has_all_point_deduct == 0) checked @endif/>
            关闭
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div class='input-group col-md-3'>
            <input type="text" name="widgets[sale][all_point_deduct]" value="{{ $item->all_point_deduct }}" class="form-control" onkeyup="this.value= this.value.match(/\d+(\.\d{0,2})?/) ? this.value.match(/\d+(\.\d{0,2})?/)[0] : ''"/>
            <span class="input-group-addon">积分</span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">不参与单品包邮地区</label>
    <div class="col-xs-12 col-sm-9 col-md-10">
        <div id="areas" class="form-control-static">{{ $item->ed_areas }}</div>
        <a href="javascript:;" class="btn btn-default selectareas" onclick="selectAreas()">添加不参加满包邮的地区</a>
        <input type="hidden" id='selectedareas' name="widgets[sale][ed_areas]" value="{{ $item->ed_areas }}"/>
        <input type="hidden" id='selectedareaids' name="widgets[sale][ed_areaids]" value="{{ $item->ed_areaids }}"/>

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">推广相关商品显示设置</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[sale][is_push]' value='1' @if($item->is_push == '1') checked @endif/>
            开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[sale][is_push]' value='0' @if( $item->is_push == 0) checked @endif/>
            关闭
        </label>
    </div>
</div>

<div id='widgets_sale' @if( $item->is_push == 0 ) style="display:none" @endif>
    <div class="form-group">
        <div class='panel panel-default'>
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>
                        推广商品选择</label>
                    <div class="col-sm-9">
                        <div class='input-group'>
                            <input type="text" maxlength="30" class="form-control" readonly/>
                            <div class='input-group-btn'>
                                <button class="btn btn-default" type="button"
                                        onclick="$('#modal-goods-push').modal();">选择商品
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
    <ul id="push_goods_li">
        @if ( $item->is_push == 1 )
            @foreach ($item->push_goods_ids as $pushgoods)
             <li id="push_goods_{{$pushgoods['id']}}" class="input-group form-group col-sm-3" style="float:left;margin: 10px 100px 30px 0;">
                <input type="hidden" name="widgets[sale][push_goods_ids][]" value="{{$pushgoods['id']}}">
                <span class="input-group-addon" style="border-left:1px solid #ccc;">{{$pushgoods['title']}}</span>
                <span class="input-group-addon" onclick="push_goods_ids_del(this);" style="background: white;cursor:pointer;">X</span>
            </li>
            @endforeach
        @endif
    </ul>
</div>
            </div>
        </div>
    </div>
</div>
<!-- 商品选择搜索 -->
<div id="modal-goods-push" class="modal fade" tabindex="-1">
    <div class="modal-dialog" style='width: 920px;'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                    ×
                </button>
                <h3>选择商品</h3></div>
            <div class="modal-body" style="height: 600px">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value=""
                               id="search-kwd-goods" placeholder="请输入商品名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default"
                                    onclick="search_goods();">搜索
                            </button></span>
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
<script language='javascript'>
    $(function () {
        $(":radio[name='widgets[sale][is_push]']").click(function () {
            if ($(this).val() == 1) {
                $("#widgets_sale").show();
            }
            else {
                $("#widgets_sale").hide();
            }
        });
    });
        
    //删除推广商品
    function push_goods_ids_del(obj) {
        $(obj).parent().remove();
    }

    //搜索商品
    function search_goods() {
        if ($.trim($('#search-kwd-goods').val()) == '') {
            Tip.focus('#search-kwd-goods', '请输入关键词');
            return;
        }
        $("#module-menus-goods").html("正在搜索....");
        
        $.get('{!! yzWebUrl('goods.goods.getSearchGoodsLevel') !!}', {
                keyword: $.trim($('#search-kwd-goods').val())
            }, function (dat) {
                $('#module-menus-goods').html(dat);
            }
        );
    }
    //选择商品
    function select_good(obj) {

        if (!($("#push_goods_"+obj.id).length > 0)) {            
            
            var str = '<li id="push_goods_'+obj.id+'" class="input-group form-group col-sm-3" style="float:left;margin: 10px 100px 30px 0;"><input type="hidden" name="widgets[sale][push_goods_ids][]" value="'+ obj.id +'"><span class="input-group-addon" style="border-left:1px solid #ccc;">'+ obj.title +'</span><span class="input-group-addon" onclick="push_goods_ids_del(this);" style="background: white;cursor:pointer;">X</span></li>';

            $('#push_goods_li').append(str);
        }
        $("#modal-goods-push .close").click();
    }

    $(function () {
        $(":radio[name='widgets[sale][point_type]']").click(function () {
            if ($(this).val() == 1) {
                $("#widgets_sale_point").show();
            }
            else {
                $("#widgets_sale_point").hide();
            }
        });
    });
</script>


@include('area.selectprovinces')