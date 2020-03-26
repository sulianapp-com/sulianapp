<!-- 供货商end -->
{{--<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>--}}

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="goods[display_order]" id="displayorder" maxlength="9" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  class="form-control" value="{{$goods['display_order']}}" />
        <span class='help-block'>数字大的排名在前,默认排序方式为创建时间，注意：输入最大数为9位数，只能输入数字</span>
        </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>{{$lang['shopname']}}</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="goods[title]" id="goodsname" class="form-control" value="{{$goods['title']}}" />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span>*</span>商品分类</label>
    <div class="col-sm-8 col-xs-12 category-container">

        {!!$catetory_menus!!}

    </div>

</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="btn btn-info col-sm-2 col-xs-2 @if (isset($type) && $type == 'edit') editCategory @else plusCategory @endif">
            添加分类
        </div>
</div>


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">品牌</label>
    <div class="col-sm-9 col-xs-12">
        <select name="goods[brand_id]" id="brand" style="width:95%">
            <option value="0">请选择品牌</option>
            @if (!empty($brands))
            @foreach ($brands as $brand)
            <option value="{{$brand['id']}}" @if ($brand['id'] == $goods['brand_id']) selected @endif>{{$brand['name']}}</option>
            @endforeach
            @endif
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品类型</label>
    <div class="col-sm-9 col-xs-12">
         <div style="float: left" id="ttttype">
             {{-- 2019-01-16 删 onclick="$('#product').show();$('#type_virtual').hide();$('#divdeposit').hide();--}}
            <label for="isshow3" class="radio-inline"><input type="radio" name="goods[type]" value="1" id="isshow3" @if (empty($goods['type']) || $goods['type'] == 1) checked="true" @endif
                onclick="$('#need_address_idx').hide();$(':radio[name=\'goods[need_address]\'][value=\'0\']').prop('checked', true);" />
                实体商品
            </label>
            <label for="isshow4" class="radio-inline"><input type="radio" name="goods[type]" value="2" id="isshow4"  @if ($goods['type'] == 2) checked="true" @endif  onclick="$('#need_address_idx').show();" /> 虚拟商品</label>
        </div>
    </div>
</div>


{{--<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品类型2</label>
    <div class="col-sm-9 col-xs-12">
        <div style="float: left" id="ttttype2">
            <label class="radio-inline"><input type="radio" name="goods[type2]" value="1" id="isshow3" @if (empty($goods['type2']) || $goods['type2'] == 1) checked="true" @endif/>
                普通商品
            </label>
            <input type="hidden" id="plugin_id" value="{{$plugin_id}}">
            @if (app('plugins')->isEnabled('lease-toy'))
                <label class="radio-inline"><input type="radio" name="goods[type2]" value="2" id="isshow4"  @if ($goods['type2'] == 2) checked="true" @endif/> 租赁商品</label>
            @endif
            @if (app('plugins')->isEnabled('video-demand'))
                <label class="radio-inline"><input type="radio" name="goods[type2]" value="3" id="isshow4"  @if ($goods['type2'] == 3) checked="true" @endif/> 课程商品</label>
            @endif
        </div>
    </div>
</div>--}}

<div id="need_address_idx" class="form-group" @if ($goods['type'] != 2) style="display: none"  @endif>
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">下单是否需要地址</label>
    <div class="col-sm-9 col-xs-12">
        <div style="float: left">
            <label class="radio-inline"><input type="radio" name="goods[need_address]" value="0" @if (empty($goods['need_address']) || $goods['need_address'] == 0) checked="true" @endif onclick="" /> 需要地址</label>
            <label class="radio-inline"><input type="radio" name="goods[need_address]" value="1" @if ($goods['need_address'] == 1) checked="true" @endif  onclick="" /> 不需要地址</label>
        </div>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>商品单位</label>
    <div class="col-sm-6 col-xs-6">
        <input type="text" name="goods[sku]" id="unit" class="form-control" value="{{$goods['sku']}}" />
        <span class="help-block">如: 个/件/包</span>

    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品属性</label>
    <div class="col-sm-9 col-xs-12" >
        <label for="isrecommand" class="checkbox-inline">
            <input type="checkbox" name="goods[is_recommand]" value="1" id="isrecommand" @if ($goods['is_recommand'] == 1) checked="true" @endif /> 推荐
        </label>
        <label for="isnew" class="checkbox-inline">
            <input type="checkbox" name="goods[is_new]" value="1" id="isnew" @if ($goods['is_new'] == 1) checked="true" @endif /> 新上
        </label>
        <label for="ishot" class="checkbox-inline">
            <input type="checkbox" name="goods[is_hot]" value="1" id="ishot" @if ($goods['is_hot'] == 1) checked="true" @endif /> 热卖
        </label>
        <label for="isdiscount" class="checkbox-inline">
            <input type="checkbox" name="goods[is_discount]" value="1" id="isdiscount" @if ($goods['is_discount'] == 1) checked="true" @endif /> 促销
        </label>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>{{$lang['mainimg']}}</label>
    <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('goods[thumb]', $goods['thumb']) !!}
        <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
        @if (!empty($goods['thumb']))
        <a href='{{yz_tomedia($goods['thumb'])}}' target='_blank'>
        <img src="{{yz_tomedia($goods['thumb'])}}" style='width:100px;border:1px solid #ccc;padding:1px' />
         </a>
        @endif
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">其他图片</label>
    <div class="col-sm-9  col-md-6 col-xs-12">

        {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('goods[thumb_url]',$goods['thumb_url']) !!}
            <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
            @if (!empty($goods['piclist']))
                 @foreach ($goods['piclist'] as $p)
                 <a href='{{yz_tomedia($p)}}' target='_blank'>
                   <img src="{{yz_tomedia($p)}}" style='height:100px;border:1px solid #ccc;padding:1px;float:left;margin-right:5px;' />
                 </a>
                 @endforeach
            @endif
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">首图视频</label>
    <div class="col-sm-9  col-md-6 col-xs-12">

        {!! app\common\helpers\ImageHelper::tplFormFieldVideo('widgets[video][goods_video]', $goods->hasOneGoodsVideo->goods_video) !!}
        {{--{!! tpl_form_field_video('widgets[video][goods_video]',$goods->hasOneGoodsVideo->goods_video) !!}--}}
            <span class="help-block">设置后商品详情首图默认显示视频，建议时长9-30秒</span>
           
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">视频封面</label>
    <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('widgets[video][video_image]', $goods->hasOneGoodsVideo->video_image) !!}
        <span class="help-block">不填默认商品主图</span>
    </div>
</div>

<div class="form-group">
    <label class=" col-sm-3 col-md-2 control-label">商品编号</label>
    <div class="col-sm-4 col-xs-12">
        <input type="text" name="goods[goods_sn]" id="productsn" class="form-control" value="{{$goods['goods_sn']}}" />
    </div>
</div>
<div class="form-group">
    <label class=" col-sm-3 col-md-2 control-label">商品条码</label>
    <div class="col-sm-4 col-xs-12">
        <input type="text" name="goods[product_sn]" id="productsn" class="form-control" value="{{$goods['product_sn']}}" />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span ></span>商品价格</label>
    <div class="col-sm-9 col-xs-12 form-inline">
        <div class="input-group form-group col-sm-3">
            <span class="input-group-addon">现价</span>
            <input type="text" name="goods[price]" id="product_price" class="form-control" value="{{$goods['price'] ? : 0}}" />
            <span class="input-group-addon">元</span>
        </div>
        <div class="input-group form-group col-sm-3">
            <span class="input-group-addon">原价</span>
            <input type="text" name="goods[market_price]" id="market_price" class="form-control" value="{{$goods['market_price']? : 0}}" />
            <span class="input-group-addon">元</span>
        </div>
        <div class="input-group form-group col-sm-3">
            <span class="input-group-addon">成本</span>
            <input type="text" name="goods[cost_price]" id="costprice" class="form-control" value="{{$goods['cost_price'] ? : 0}}" />
            <span class="input-group-addon">元</span>
        </div>
        <span class='help-block'>尽量填写完整，有助于于商品销售的数据分析</span>

    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">重量</label>
    <div class="col-sm-6  col-xs-12">
        <div class="input-group col-md-3">
            <input type="text" name="goods[weight]" id="weight" class="form-control" value="{{$goods['weight']?$goods['weight']:0}}" />
            <span class="input-group-addon">克</span>
        </div>
        <div class='help-block'>商品重量设置空或0，取首重（设置配送模板相关）</div>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>库存</label>
    <div class="col-sm-6 col-xs-12">
        <div class="input-group  col-md-3 form-group col-sm-3">
            <input type="text" name="goods[stock]" id="total" class="form-control" value="{{$goods['stock']}}" />
            <span class="input-group-addon">件</span>
        </div>
        <span class="help-block">商品的剩余数量, 如启用多规格或为虚拟卡密产品，则此处设置无效，请移至“商品规格”或“虚拟物品插件”中设置</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span >*</span>虚拟销量</label>
    <div class="col-sm-6 col-xs-12">
        <div class="input-group  col-md-3 form-group col-sm-3">
            <input type="text" onkeyup="value=value.replace(/[^\d]/g,'')" name="goods[virtual_sales]" id="total" class="form-control" value="{{$goods['virtual_sales']}}" />
            <span class="input-group-addon">件</span>
        </div>
        <span class="help-block">前端真实销量 = 虚拟销量 + 真实销量</span>
    </div>
</div>


<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">减库存方式</label>
    <div class="col-sm-9 col-xs-12">
        <label for="totalcnf1" class="radio-inline"><input type="radio" name="goods[reduce_stock_method]" value="0" id="totalcnf1" @if (empty($goods) || $goods['reduce_stock_method'] == 0) checked="true" @endif /> 拍下减库存</label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf2" class="radio-inline"><input type="radio" name="goods[reduce_stock_method]" value="1" id="totalcnf2"  @if (!empty($goods) && $goods['reduce_stock_method'] == 1) checked="true" @endif /> 付款减库存</label>
        &nbsp;&nbsp;&nbsp;
        <label for="totalcnf3" class="radio-inline"><input type="radio" name="goods[reduce_stock_method]" value="2" id="totalcnf3"  @if (!empty($goods) && $goods['reduce_stock_method'] == 2) checked="true" @endif /> 永不减库存</label>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">不可退货退款</label>
    <div class="col-sm-9 col-xs-12">
        <label for="norefund1" class="radio-inline">
            <input type="radio" name="goods[no_refund]" value="1" id="norefund1" @if ($goods['no_refund'] == 1) checked="true" @endif /> 是</label>
        &nbsp;&nbsp;&nbsp;
        <label for="norefund2" class="radio-inline">
            <input type="radio" name="goods[no_refund]" value="0" id="norefund2"  @if ($goods['no_refund'] == 0) checked="true" @endif /> 否</label>
        <span class="help-block"></span>

    </div>
</div>

<!-->
@if(\app\common\services\PermissionService::can('goods_goods_putaway'))
    @section('isputaway')
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$lang['isputaway']}}</label>
            <div class="col-sm-9 col-xs-12">
                <label for="isshow1" class="radio-inline"><input type="radio" name="goods[status]" value="1" id="isshow1" @if ($goods['status'] == 1) checked="true" @endif /> 是</label>
                <label for="isshow2" class="radio-inline"><input type="radio" name="goods[status]" value="0" id="isshow2" @if ($goods['status'] == 0) checked="true" @endif /> 否</label>
                <span class="help-block"></span>
            </div>
        </div>
    @show
@endif
<script type="text/javascript">
    $('#brand').select2();

    $('.plusCategory').click(function () {
        appendHtml = $(this).parents().find('.tpl-category-container').html();

        $(this).parents().find('.category-container').append('<div class="row row-fix tpl-category-container">' + appendHtml + '<div>');
    });

    $('.editCategory').click(function () {
        appendHtml = $(this).parents().find('.tpl-category-container').html();

        $(this).parents().find('.category-container').append('<div class="row row-fix tpl-category-container">' + appendHtml + '<div>');
        $('.category-container').children(':last').children().children('select').find("option[value='0']").attr("selected",true)
        var seconde_category = $('.category-container').children(':last').children().children('select:eq(1)');
        var third_category = $('.category-container').children(':last').children().children('select:eq(2)');

        if (seconde_category.length > 0) {
            seconde_category.children(':gt(0)').remove();
        }
        if (third_category.length > 0) {
            third_category.children(':gt(0)').remove();
        }
    });

    $(document).on('click', '.delCategory', function () {
        var count = $(this).parents('.tpl-category-container').siblings('.tpl-category-container').length;

        if (count >= 1) {
            $(this).parents('.tpl-category-container').remove();
        } else {
            alert('商品分类必选');
        }


    });
</script>
{{--@section('js')--}}
    {{--<script>--}}
        {{--require(['select2'],function() {--}}
            {{--$('#brand').select2();--}}
        {{--})--}}
    {{--</script>--}}
 {{--@stop--}}