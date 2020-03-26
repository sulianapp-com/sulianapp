<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠方式</label>
    <div class="col-sm-9 col-xs-12">
        <input type="hidden" name="coupontype" value="0"/>
        <label class="radio-inline" ><input type="radio" name="coupon[coupon_method]" id="couponmethod" onclick='showbacktype(0)' value="1" checked>立减</label>
        <label class="radio-inline"><input type="radio" name="coupon[coupon_method]" id="couponmethod" onclick='showbacktype(1)' value="2" @if($coupon['coupon_method']==2)checked @endif>打折</label>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
    <div class="col-sm-2 backtype backtype0" @if($coupon['coupon_method']!=1)style='display:none' @endif>
        <div class='input-group'>
            <span class='input-group-addon'>立减</span>
            <input type='text' class='form-control' name='coupon[deduct]' value="{{$coupon['deduct']?intval($coupon['deduct']):0}}"/>
            <span class='input-group-addon'>元</span>
        </div>
    </div>
    <div class="col-sm-2 backtype backtype1"  @if($coupon['coupon_method']!=2)style='display:none' @endif>
        <div class='input-group'>
            <span class='input-group-addon'>打</span>
            <input type='text' class='form-control' name='coupon[discount]'  placeholder='1-9' value="{{$coupon['discount']?intval($coupon['discount']):0}}"/>
            <span class='input-group-addon'>折</span>
        </div>
    </div>
</div>

