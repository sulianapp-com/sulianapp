<div class="form-group key_item">

    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义键</label>

    <div class="col-sm-9" style="padding:0;padding-left:15px;">
        <input type="text" readonly="readonly" name="temp[tp_kw][]" class="form-control" value="{{$temp2['keywords']}}{{$tpkw}}" placeholder="键名"/>
    </div>
</div>

<div class="form-group key_item">

    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>

    <div class="col-sm-7" style="padding:0;padding-left:15px;">
        <textarea name="temp[tp_value][]" class="form-control" placeholder="键值" rows="5">{{$temp2['value']}}</textarea>
    </div>
    <div class="col-sm-1" style="padding:0">
        <input type="color" name="temp[tp_color][]" value="{{$temp2['color']}}" style="width:32px;height:32px;"/>
    </div>
    <a class="btn btn-default" href='javascript:;' onclick="$(this).closest('.key_item').prev().remove();$(this).closest('.key_item').remove()"><i
                class='fa fa-remove'></i> 删除</a>
</div>

