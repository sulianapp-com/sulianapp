<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品邀请页面</label>
    <div class="col-sm-9 col-xs-12">
        <label class='radio-inline'>
            <input type='radio' name='widgets[invite_page][status]' value='1' @if($data->status == '1') checked="checked" @endif/>
            开启
        </label>
        <label class='radio-inline'>
            <input type='radio' name='widgets[invite_page][status]' value='0' @if(empty($data->status))  checked="checked" @endif/>
            关闭
        </label>
    </div>
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
</div>