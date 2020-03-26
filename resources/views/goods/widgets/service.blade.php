
    {{--<div class="form-group">--}}
        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否支持退换货</label>--}}
        {{--<div class="col-sm-9 col-xs-12">--}}
            {{--<label class='radio-inline'>--}}
                {{--<input type='radio' name='widgets[service][is_refund]' value='1' @if($service->is_refund == '1') checked @endif/>--}}
                {{--开启--}}
            {{--</label>--}}
            {{--<label class='radio-inline'>--}}
                {{--<input type='radio' name='widgets[service][is_refund]' value='0' @if($service->is_refund == '0') checked @endif/>--}}
                {{--关闭--}}
            {{--</label>--}}
            {{--<!-- <span class='help-block'></span> -->--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否自动上下架</label>
        <div class="col-sm-9 col-xs-12">
            <label class='radio-inline'>
                <input type='radio' name='widgets[service][is_automatic]' value='1' @if($service->is_automatic == '1') checked="checked" @endif/>
                是
            </label>
            <label class='radio-inline'>
                <input type='radio' name='widgets[service][is_automatic]' value='0' @if(empty($service->is_automatic))  checked="checked" @endif/>
                否
            </label>
            <span class='help-block'>商品在选择时间内自动上架，过期自动下架</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">上下架时间</label>
        <div class="col-sm-9 col-xs-12">
             {!! app\common\helpers\DateRange::tplFormFieldDateRange('widgets[service][time]', [
            'starttime'=>date('Y-m-d H:i', $time['starttime']),
            'endtime'=>date('Y-m-d H:i',$time['endtime']),
            'start'=>0,
            'end'=>0
            ], true) !!}
        </div>
    </div>
<script language='javascript'>

</script>


