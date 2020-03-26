<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">限时开关</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[limitbuy][status]' value='1' @if($data->status == '1') checked="checked" @endif/>
            开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[limitbuy][status]' value='0' @if(empty($data->status))  checked="checked" @endif/>
            关闭
        </label>
    </div>
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">限时时间</label>
    <div>
        {!! app\common\helpers\DateRange::tplFormFieldDateRange('widgets[limitbuy][time]', [
        'starttime'=>date('Y-m-d H:i', $starttime),
        'endtime'=>date('Y-m-d H:i',$endtime),
        'start'=>0,
        'end'=>0
        ], true) !!}
    </div>
</div>