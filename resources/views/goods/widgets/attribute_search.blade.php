<div class='panel panel-default'>

    <div class='panel-heading'><h3>年龄设置</h3></div>
    <div class='panel-body'>
        <div class='panel-body'>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启：</label>
                <div class="col-sm-4 col-xs-6">
                    <label class="radio-inline">
                        <input type="radio" name="widgets[attribute_search][is_show]" value="1" @if ($attribute_search['is_show'] == 1) checked="checked" @endif />
                        开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="widgets[attribute_search][is_show]" value="0" @if (empty($attribute_search['is_show'])) checked="checked" @endif />
                        关闭
                    </label>
                </div>
            </div>


        </div>
    </div>


</div>