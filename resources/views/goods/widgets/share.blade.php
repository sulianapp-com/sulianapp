
{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">购买强制关注</label>--}}
    {{--<div class="col-sm-6 col-xs-6">--}}
        {{--<label class="radio-inline"><input type="radio" name="widgets[share][need_follow]" value="0"--}}
                                           {{--@if ( empty($share->need_follow) ) checked="true" @endif />--}}
            {{--不需关注</label>--}}
        {{--<label class="radio-inline"><input type="radio" name="widgets[share][need_follow]" value="1"--}}
                                           {{--@if ( $share->need_follow == 1) checked="true" @endif /> 必须关注</label>--}}
    {{--</div>--}}
{{--</div>--}}
{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">未关注提示</label>--}}
    {{--<div class="col-sm-6 col-xs-6">--}}
        {{--<input type='text' class="form-control" name="widgets[share][no_follow_message]"--}}
               {{--value="{{ $share->no_follow_message }}"/>--}}
        {{--<span class='help-block'>购买商品必须关注，如果未关注，弹出的提示，如果为空默认为“如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~”</span>--}}
    {{--</div>--}}
{{--</div>--}}

{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">关注引导</label>--}}
    {{--<div class="col-sm-6 col-xs-6">--}}
        {{--<input type='text' class="form-control" name="widgets[share][follow_message]"--}}
               {{--value="{{ $share->follow_message}}"/>--}}
        {{--<span class='help-block'>购买商品必须关注，如果未关注，跳转的连接，如果为空默认为系统关注页</span>--}}
    {{--</div>--}}
{{--</div>--}}

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="widgets[share][share_title]" id="share[share_title]" class="form-control"
               value="{{ $share->share_title }}"/>
        <span class='help-block'>如果不填写，默认为商品名称</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图标</label>
    <div class="col-sm-9 col-xs-12">
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('widgets[share][share_thumb]', $share->share_thumb) !!}
        <span class='help-block'>如果不选择，默认为商品缩略图片</span>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
    <div class="col-sm-9 col-xs-12">
        <textarea name="widgets[share][share_desc]" class="form-control">{{ $share->share_desc }}</textarea>
        <span class='help-block'>如果不填写，默认为店铺名称</span>
    </div>
</div>