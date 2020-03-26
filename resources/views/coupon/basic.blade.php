<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
    <div class="col-sm-5">
        <input type="text" name="coupon[display_order]" class="form-control" value="{{isset($coupon['display_order']) ? $coupon['display_order'] : 0 }}"  />
        <span class='help-block'>数字越大越靠前</span>
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 优惠券名称</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="coupon[name]" id="couponname" class="form-control" value="{{$coupon['name']}}"  />
    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type="radio" name="coupon[status]" value="1" checked/>开启
        </label>
        <label class='radio-inline'>
            <input type="radio" name="coupon[status]" value="0" @if($coupon['status']!=='' && $coupon['status']==0) checked @endif/>不开启
        </label>
        <span class='help-block'>关闭后,用户无法领取, 但是已经被领取的可以继续使用</span>
    </div>
</div>

{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">缩略图</label>--}}
    {{--<div class="col-sm-9 col-xs-12">--}}
        {{--{!! app\common\helpers\ImageHelper::tplFormFieldImage('coupon[thumb]', $coupon['thumb']) !!}--}}
    {{--</div>--}}
{{--</div>--}}