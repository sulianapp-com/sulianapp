{{--<div class="form-group">--}}
    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">语言编码</label>--}}
    {{--<div class="col-sm-9 col-xs-12">--}}
        {{--<label class="radio-inline">--}}
            {{--<input type="radio" name="setdata[lang]" value="zh_cn"--}}
                   {{--@if($set['lang'] == 'zh_cn') checked="checked" @endif /> 中文</label>--}}
        {{--<label class="radio-inline">--}}
            {{--<input type="radio" name="setdata[lang]" value="en"--}}
                   {{--@if($set['lang'] == 'en') checked="checked" @endif /> english</label>--}}
    {{--</div>--}}
{{--</div>--}}

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">测试</label>
    <div class="col-sm-9 col-xs-12">
        <input type="text" name="setdata[test]" class="form-control" value="{{$set['test']}}" />
    </div>
</div>




